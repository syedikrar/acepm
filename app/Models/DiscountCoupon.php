<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class DiscountCoupon.
 *
 * @package namespace App\Models;
 */
class DiscountCoupon extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_name',
        'coupon',
        'percentage',
        'status',
        'decision',
        'requested_on',
        'availed_on'
    ];

}
