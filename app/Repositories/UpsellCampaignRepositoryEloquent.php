<?php

namespace App\Repositories;

use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\UpsellCampaignRepository;
use App\Models\UpsellCampaign;
use App\Validators\UpsellCampaignValidator;

/**
 * Class UpsellCampaignRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UpsellCampaignRepositoryEloquent extends BaseRepository implements UpsellCampaignRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UpsellCampaign::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function create(array $attributes){
        $params = [
            'plan' => json_encode($attributes['plan']),
            'type' => $attributes['type'],
            'title'         => $attributes['title'],
            'message'       => $attributes['message'],
            'repeat_after'   => $attributes['repeat_after'],
            'max_tries'      => $attributes['max_tries'],
            'campaign_starts' => Carbon::createFromFormat('m-d-y', $attributes['campaign_range']['start'])->startOfDay(),
            'campaign_expires' => Carbon::createFromFormat('m-d-y', $attributes['campaign_range']['end'])->endOfDay()
        ];
        return parent::create($params);
    }

    public function active($data){
        $currentDate = Carbon::now()->startOfDay();
        $plan = $data['plan'];

        $campaign = UpsellCampaign::where('status', '=', '1')
            ->where('campaign_starts', '<=', $currentDate)
            ->where('campaign_expires', '>=', $currentDate)
            ->Where(function($query) use ($plan)
            {
                $query->whereNull('plan')
                    ->orWhere('plan', 'LIKE', '%'.$plan.'%')
                    ->orWhere('plan', 'LIKE', '%all%');
            })
            ->orderBy('created_at', 'Asc')->first();

        return $campaign;
    }
}
