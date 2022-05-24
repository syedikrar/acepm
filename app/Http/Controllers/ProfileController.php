<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Skills;
use App\Services\ProfileService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{

    /**
     * @var ProfileService
     */
    private $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    /**
     * Update user
     *
     * @param Request $request
     *
     * @return App\Models\User
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request)
    {
        if ($request->input('data') != null) {
            $result = json_decode($request->input('data'), true);
            unset($request['data']);
            $request->request->add($result);
        }
        $userId = $request->user()->id;
        $rules = [
            'name' => 'required|string|max:191',
            'email' => 'required|string|email|max:191|unique:users,email,' . $userId,
            'password' => 'nullable|string|min:6|confirmed'
        ];
        $this->validate($request, $rules);
        $user = $request->user();
        $path = '';
        if ($request->hasFile('logo')) {
            $file = $request->file('logo');

            $name = $file->getClientOriginalName();
            $path = preg_replace("/\s+/", "", '/images/profile_images/'.$userId .'/'.time().'_'.$name);
            Storage::disk('public_uploads')->put($path, file_get_contents($file));
            $user = User::find($userId);
            $this->deleteBackgroundImage(null, $user);
        }
        $user->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'description' => $request->input('description'),
            'languages' => $request->input('languages'),
            'country' => $request->input('country'),
            'profile_picture' =>  ($request->file('logo')) ? $path : null,
            'social_links' => $request->input('social_links'),
        ]);


        if ($request->input('password')) {
            $user->password = bcrypt($request->input('password'));
        }
        $user->save();
        if($user->role == 'seller') $this->createLinkedEntities($request);

        $user = $user->load(['skills', 'education']);
        return response()->json(compact('user'));
    }

    public function deleteBackgroundImage($id, User $user){

        $resp = [
            'status'    => 'success',
            'message'   => 'Image deleted successfully',
        ];

        try {
            $file_url =  $user->profile_picture;
            $user->profile_picture = '';
            Storage::disk('public_uploads')->delete($file_url);
            $user->save();

        }catch(Exception $e){
            $resp = [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }

        return response()->json($resp);
    }

    public function getUsers(Request $request)
    {
        $shop = Shop::where('domain', $request->cookie('ace-shop'))->first();
        return response()->json($shop != null ? $shop->users : []);
    }

    public function teamTasks(Request $request)
    {
        $results =  Shop::with(['users' => function($query){
            $query->with(['boards' => function($board){
                $board->with(['cards' => function($cards){
                    $cards->with(['subTasks']);
                }]);
            }]);
        }])
            ->where([
                'domain'                => $request->cookie('ace-shop'),
            ])->first();
        return response()->json($results != null ? $results : []);
    }

    public function updateStatus($id, Request $request)
    {
        $user = User::find($id);
        $user->fill([
            'owner'         => $request['owner'] == true ? 'owner' : 'staff',
            'approved_at'   => $request['approved_at'] == null || $request['approved_at'] == false ? null : Carbon::now()
        ]);
        $user->save();
        return response()->json([
            'status'    => true,
            'message'   => 'User Updated.',
            'data'      => compact('user')
        ]);
    }

    public function delete($id, Request $request)
    {
        try {
            $user = User::find($id);
            $user->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false]);
        }
    }

    public function getBestSellers(Request $request)
    {
       return response()->json([
           'users' => $this->profileService->getBestSellers($request)
       ]);
    }

    private function createLinkedEntities(Request $request){
        //----------Insert Education
        $educationData = array_merge((array)$request->get('education'), ['user_id' => $request->get('id')]);
        unset($educationData['created_at']); unset($educationData['updated_at']);
        auth()->user()->education()->updateOrCreate($educationData);

        //----------Insert Skills
        auth()->user()->skills()->delete();
        $skillsData = [];
        foreach ($request->get('skills') as $value)
            $skillsData[] = ['name' => $value, 'user_id' => $request->get('id')];
        Skills::insert($skillsData);
    }

}
