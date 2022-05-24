<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\BillingRepository;
use App\Models\Billing;
use App\Validators\BillingValidator;

/**
 * Class BillingRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BillingRepositoryEloquent extends BaseRepository implements BillingRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Billing::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function create(array $attributes)
    {
        $params = [
            'shop_id'               => $attributes['shop_id'],
            'shop_domain'           => $attributes['shop_domain'],
            'shopify_billing_id'    => $attributes['id'],
            'plan'                  => $attributes['plan'],
            'price'                 => floatval($attributes['price']),
            'type'                  => 'app',
            'status'                => $attributes['status'],
            'activated_on'          => null,
            'trial_days'            => $attributes['trial_days'],
            'billing_on'            => null
        ];
        return parent::create($params);
    }

}
