<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Prettus\Repository\Contracts\Transformable;
use Prettus\Repository\Traits\TransformableTrait;

/**
 * Class Card.
 *
 * @package namespace App\Models;
 */
class Card extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'board_id',
        'title',
        'description',
        'pane_index',
        'assignee_id',
        'pre_requisite_id',
        'due_date',
        'label',
        'tags',
        'priority'
    ];

    protected $casts = [
        'tags' => 'array'
    ];

    public function subTasks(){
        return $this->hasMany(SubTask::class);
    }

    protected $with = [
        'attachments',
        'timeTracker'
    ];

    public function timeTracker(){
        return $this->hasMany(TimeTracker::class);
    }

    public function shopifyIntegrations()
    {
        return $this->hasMany(ShopifyIntegration::class);
    }

    public function attachments(){
        return $this->hasMany(CardAttachment::class);
    }

    public function comments(){
        return $this->hasMany(Comment::class);
    }

}
