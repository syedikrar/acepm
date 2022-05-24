<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class TimeTracker.
 *
 * @package namespace App\Models;
 */
class TimeTracker extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'card_id',
        'description',
        'time_spent',
        'time_remaining',
        'date_started'
    ];

    public function card(){
        return $this->belongsTo(Card::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
