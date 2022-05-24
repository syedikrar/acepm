<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class FreePass.
 *
 * @package namespace App\Models;
 */
class FreePass extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['shop', 'active', 'last_checked'];

    /**
     * @var string[]
     */
    protected $casts = [
        'active'        => 'boolean',
        'last_checked'  => 'datetime'
    ];

}
