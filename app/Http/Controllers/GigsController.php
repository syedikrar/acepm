<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Contract;
use App\Models\Gig;
use App\Models\GigJob;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GigsController extends Controller
{
    public function categories(Request $request) {
        if($request->get('gigs', false)) $result = Category::whereNull('parent')->with(["gigs", "gigs.galleries", "gigs.packages", "gigs.category", "gigs.sub_category"])->get();
        else $result = Category::whereNull('parent')->with('subCategories')->get();

        return response()->json($result);
    }

    public function sub_categories($id)
    {
        return response()->json(Category::where("parent", $id)->get());
    }

    public function all()
    {
        $gigs = Gig::where("user_id", auth()->user()->id)
            ->with(["galleries", "packages", "questions", "category", "sub_category"])
            ->get();

        return response()->json($gigs);
    }

    public function show($id)
    {
        return response()->json(Gig::with(["galleries", "packages", "questions", "category", "sub_category", "user"])->find($id));
    }

    public function gigsOfWeek(Request $request){
        $date = Carbon::today()->subDay(7);
        $gigs = Gig::with(["galleries", "category"])
            ->whereHas('reviews', function ($query) use ($date){
                $query->where('created_at', '>=', $date);
            })
            ->withCount('reviews')
            ->orderByDesc('reviews_count')
            ->take(5)
            ->get();

        return response()->json($gigs);
    }

  public function save(Request $request) {
      $gig = json_decode($request->gig,true);
        $gig['user_id'] = auth()->user()->id;
        $gig['search_terms'] = json_encode($gig['search_terms']);


        // Create Gig
        $gig_db = Gig::create($gig);

        // Create Gig Packages
        $gig_db->packages()->createMany($gig['packages']);

        // Create Gig Questions
        $gig_db->questions()->createMany($gig['questions']);

        // Save Images for Gig
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $file) {
                $imageName = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path() . '/images/gigs/', $imageName);
                $gig_db->galleries()->create([
                    'image' => $imageName
                ]);
            }
        }


        return response()->json(['message' => 'Gig Saved Successfully!', 'status' => 200]);

    }

    public function order(Request $request)
    {
        $gig = $request->gig;
        $questions = $gig['questions'];
        $answers = "[]";

        if (count($questions) > 0) {
            $answers = collect($questions)->map(function ($item, $key) {
                return [
                    'question_id' => $item['id'],
                    'answer' => isset($item['answer']) ? $item['answer'] : ''
                ];
            })->toJson();
        }

        $contract = Contract::create([
            'gig_id' => $gig['id'],
            'package_id' => $request->package,
            'user_id' => auth()->user()->id,
            'answers' => $answers
        ]);

        return response()->json(['message' => 'Order Successfully Created!', 'status' => 200]);
    }

    public function contracts() {
        $contracts = Contract::with("gig")
                        ->with("gig.user")
                        ->with("package")
                        ->with("user")
                        ->get();
        return response()->json($contracts);
    }

    public function sellerContracts()
    {
        $contracts = Contract::with("gig")
            ->with("gig.user")
            ->with("package")
            ->with("user")
            ->whereHas("gig.user", function($q) {
                $q->where("id",auth()->user()->id);
            })
            ->get();
        return response()->json($contracts);
    }

    public function deleteContract(Contract $contract) {
        $contract->delete();

        return response()->json(['message' => 'Contract Successfully Deleted!', 'status' => 200]);
    }

    public function saveJob(Request $request) {
        GigJob::create([
            'user_id' => auth()->user()->id,
            'descriptions' => $request->descriptions,
            'category_id' => $request->category_id,
            'sub_category_id' => $request->sub_category_id,
            'delivery' => $request->delivery,
            'price' => $request->price,
            'card_id' => $request->card['id']
        ]);

        return response()->json(['message' => 'Job Successfully Created!', 'status' => 200]);
    }

    public function getJobs() {
        $jobs = GigJob::with("category")
                    ->with("sub_category")
                    ->get();

        return response()->json($jobs);
    }
}
