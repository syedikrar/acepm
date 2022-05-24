<?php

namespace App\Http\Controllers;

use App\Contracts\ShopRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Log;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\UpsellCampaignCreateRequest;
use App\Http\Requests\UpsellCampaignUpdateRequest;
use App\Contracts\UpsellCampaignRepository;
use App\Validators\UpsellCampaignValidator;

/**
 * Class UpsellCampaignsController.
 *
 * @package namespace App\Http\Controllers;
 */
class UpsellCampaignsController extends Controller
{
    /**
     * @var UpsellCampaignRepository
     */
    protected $repository;
    /**
     * @var ShopRepository
     */
    private $shopRepo;

    /**
     * UpsellCampaignsController constructor.
     *
     * @param UpsellCampaignRepository $repository
     */
    public function __construct(UpsellCampaignRepository $repository, ShopRepository $shopRepo)
    {
        $this->repository = $repository;
        $this->shopRepo = $shopRepo;

    }

    public function active(Request $request){
        $shop = $request->cookie('ace-shop');
        $shop = $this->shopRepo->findByField('domain', $shop)->first();

        //----------Consider campaign only if a shop installation date is at least a week older
        $installedWeekCount = Carbon::now()->diffInWeeks($shop->created_at);

        if($shop != null && $installedWeekCount > 0){
            $data = $request->all();
            $data['plan'] = $shop->billing->name;
            $campaign = $this->repository->active($data);
        }else $campaign = null;

        return response()->json([
            'campaign' => $campaign
        ]);
    }


    public function save(Request $request){

        $resp = [
            'status' => true,
            'message'=> 'Campaign Added.'
        ];

        try {
            $data = $request->get('data');
            $this->repository->create($data);
        } catch (\Exception $e) {
            $resp['status'] = false;
            $resp['message'] = $e->getMessage();
        }

        return response()->json($resp);
    }

    public function recordStats(Request $request){
        $id = $request->get('campaign_id');
        $event = $request->get('event');
        try{
            $this->repository->find($id)->increment($event);
        }catch (\Exception $exception){
            Log::info("Upsell campaigns stats for Shop: ". $exception->getMessage());
        }

        return response()->json(['status' => true]);
    }

    public function get(){
        return response()->json([
            'campaigns' => $this->repository->all()
        ]);
    }

    public function delete($id){
        try {
            $this->repository->delete($id);
            return response()->json(['status' => true, 'message' => 'Campaign removed.']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()]);
        }
    }

    public function toggle(Request $request){
        $data = $request->all();
        $this->repository->update(['status'=> $data['status']], $data['id']);
        return  response()->json(['status' => true, 'message' => 'Campaign status updated.']);
    }
}
