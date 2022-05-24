<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\reviewsRepository;
use App\Models\Reviews;
use App\Validators\ReviewsValidator;

/**
 * Class ReviewsRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ReviewsRepositoryEloquent extends BaseRepository implements ReviewsRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return Reviews::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
