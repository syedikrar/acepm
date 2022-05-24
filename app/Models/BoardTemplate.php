<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class BoardTemplate.
 *
 * @package namespace App\Models;
 */
class BoardTemplate extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'field_type_id',
        'name',
        'background_image',
        'description',
        'content',
        'approved_at',
        'views',
        'installs'
    ];

    protected $casts = [
        'content' => 'array'
    ];

    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
