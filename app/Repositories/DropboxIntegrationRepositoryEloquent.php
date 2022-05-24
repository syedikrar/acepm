<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\DropboxIntegrationRepository;
use App\Models\DropboxIntegration;
use App\Validators\DropboxIntegrationValidator;

/**
 * Class DropboxIntegrationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class DropboxIntegrationRepositoryEloquent extends BaseRepository implements DropboxIntegrationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return DropboxIntegration::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
