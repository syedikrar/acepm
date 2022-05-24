<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Billing.
 *
 * @package namespace App\Models;
 */
class Billing extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'shop_id',
        'shop_domain',
        'shopify_billing_id',
        'plan',
        'price',
        'type',
        'status',
        'activated_on',
        'trial_days',
        'billing_on',
        'refunded'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'activated_on', 'billing_on'];

    /**
     * @return BelongsTo
     */
    public function shop()
    {
        return $this->belongsTo('App\Models\Shop');
    }

    /**
     * @return bool
     */
    public function onTrial()
    {
        $now = Carbon::now();
        return $now->lessThan($this->billing_on);
    }
}
