<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $guarded = [];

    public function gig() {
        return $this->belongsTo(Gig::class);
    }

    public function package() {
        return $this->belongsTo(GigPackage::class, "package_id");
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
