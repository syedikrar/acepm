<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Board.
 *
 * @package namespace App\Models;
 */
class Board extends BaseModel
{

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shop_id',
        'field_type_id',
        'name',
        'background_image',
        'panes'
    ];

    protected $casts = [
        'panes' => 'array'
    ];

    protected $with = [
        'members',
        'creator'
    ];

    public function fieldType()
    {
        return $this->belongsTo(FieldType::class);
    }

    public function cards()
    {
        return $this->hasMany(Card::class);
    }

    public function creator()
    {
        return $this->members()->wherePivot('role', 'creator');
    }

    public function members(){
        return $this->belongsToMany(User::class)->withPivot(['role']);
    }

}
