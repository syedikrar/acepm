<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gig extends Model
{
    protected $fillable = ['title','category_id','sub_category_id','search_terms','user_id', 'descriptions'];

    public function category() {
        return $this->belongsTo(Category::class);
    }

    public function sub_category() {
        return $this->belongsTo(Category::class, "sub_category_id");
    }

    public function packages() {
        return $this->hasMany(GigPackage::class);
    }

    public function questions() {
        return $this->hasMany(GigQuestions::class);
    }

    public function galleries() {
        return $this->hasMany(GigGallery::class);
    }

    public function reviews() {
        return $this->hasMany(Reviews::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
