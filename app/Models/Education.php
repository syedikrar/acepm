<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Education extends Model
{
    protected $fillable = [
        'user_id', 'country', 'university', 'title', 'major', 'year'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
