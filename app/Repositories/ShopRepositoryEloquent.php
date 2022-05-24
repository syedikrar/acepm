<?php

namespace App\Repositories;

use App\Models\User;
use App\Notifications\ApproveRequest;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\ShopRepository;
use App\Models\Shop;
use App\Validators\ShopValidator;

/**
 * Class ShopRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShopRepositoryEloquent extends BaseRepository implements ShopRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Shop::class;
    }



    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    public function createOwner($shopInfo)
    {
        // ---- see if the user is already created
        $user = User::where('email', $shopInfo->email)->first();
        if ($user != null) return $user;

        return User::create([
            'name'              => $shopInfo->shop_owner,
            'email'             => $shopInfo->email,
            'password'          => bcrypt('3r@0f3c0m'),
            'role'              => 'owner',
            'approved_at'       => Carbon::now(),
            'email_verified_at' => Carbon::now()
        ]);
    }

}
