<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GigJob extends Model
{
    protected $guarded = [];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function sub_category() {
        return $this->belongsTo(Category::class,"sub_category_id");
    }
}
