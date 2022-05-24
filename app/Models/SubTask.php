<?php

namespace App\Models;

/**
 * Class SubTask.
 *
 * @package namespace App\Models;
 */
class SubTask extends BaseModel
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'card_id',
        'assignee_id',
        'title',
        'done',
        'due_date'
    ];

    protected $with = [
        'assignee'
    ];

    public function card(){
        return $this->belongsTo(Card::class);
    }

    public function assignee(){
        return $this->belongsTo(User::class, 'assignee_id', 'id');
    }

}
