<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\FreePassRepository;
use App\Models\FreePass;
use App\Validators\FreePassValidator;

/**
 * Class FreePassRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class FreePassRepositoryEloquent extends BaseRepository implements FreePassRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return FreePass::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
