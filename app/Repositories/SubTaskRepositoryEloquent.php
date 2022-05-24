<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\SubTaskRepository;
use App\Models\SubTask;
use App\Validators\SubTaskValidator;

/**
 * Class SubTaskRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class SubTaskRepositoryEloquent extends BaseRepository implements SubTaskRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return SubTask::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
