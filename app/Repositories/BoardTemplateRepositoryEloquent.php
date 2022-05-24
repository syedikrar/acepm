<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\BoardTemplateRepository;
use App\Models\BoardTemplate;
use App\Validators\BoardTemplateValidator;

/**
 * Class BoardTemplateRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class BoardTemplateRepositoryEloquent extends BaseRepository implements BoardTemplateRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return BoardTemplate::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
