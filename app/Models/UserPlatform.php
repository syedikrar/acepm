<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class UserPlatform.
 *
 * @package namespace App\Models;
 */
class UserPlatform extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'email',
        'access_token',
        'provider',
        'provider_id',
        'provider_name',
        'provider_user_name',
        'provider_photo',
        'meta_json'
    ];

}
