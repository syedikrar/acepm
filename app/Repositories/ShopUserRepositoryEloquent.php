<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\ShopUserRepository;
use App\Models\ShopUser;
use App\Validators\ShopUserValidator;

/**
 * Class ShopUserRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShopUserRepositoryEloquent extends BaseRepository implements ShopUserRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ShopUser::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
