<?php
/**
 * Created by PhpStorm.
 * User: Bilal
 * Date: 3/8/2018
 * Time: 02:23 PM
 */

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;

class ProfileService
{
    public function __construct()
    {
    }

    public function getBestSellers(){
//        $date = Carbon::today()->subDay(7);
//        $bestSellers = User::with('education')
//            ->whereHas('reviews', function ($query) use ($date){
//                $query->where('ratings', '>=', 4)
//                    ->where('created_at', '>=', $date);
//            })
//            ->withCount('reviews')
//            ->orderByDesc('reviews_count')
//            ->take(5)
//            ->get();
//
//        $bestSellers = User::all();
//        return $bestSellers;
        return User::where('role' , '=' , 'seller')->with('education')->get();
    }

}