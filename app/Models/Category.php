<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    public function gigs() {
        return $this->hasMany(Gig::class, 'category_id');
    }

    public function subCategories() {
        return $this->hasMany(Category::class,'parent');
    }

}
