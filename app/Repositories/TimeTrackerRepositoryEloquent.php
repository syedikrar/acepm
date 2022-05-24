<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\TimeTrackerRepository;
use App\Models\TimeTracker;
use App\Validators\TimeTrackerValidator;

/**
 * Class TimeTrackerRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class TimeTrackerRepositoryEloquent extends BaseRepository implements TimeTrackerRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return TimeTracker::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
