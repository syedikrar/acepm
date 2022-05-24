<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UpsellCampaign.
 *
 * @package namespace App\Models;
 */
class UpsellCampaign extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan',
        'type',
        'title',
        'message',
        'repeat_after',
        'max_tries',
        'campaign_starts',
        'campaign_expires',
        'status'
    ];

}
