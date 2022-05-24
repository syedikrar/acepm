<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class CardAttachment.
 *
 * @package namespace App\Models;
 */
class CardAttachment extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_id', 'user_id', 'path', 'name'
    ];

    public function card(){
        return $this->belongsTo(Card::class);
    }

}
