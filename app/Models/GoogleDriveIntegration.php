<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class GoogleDriveIntegration.
 *
 * @package namespace App\Models;
 */
class GoogleDriveIntegration extends Model implements Transformable
{
    use TransformableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'file_id',
        'name',
        'type',
        'icon_url',
        'url',
        'card_id',
    ];

}
