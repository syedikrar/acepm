<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\ShopifyIntegrationRepository;
use App\Models\ShopifyIntegration;
use App\Validators\ShopifyIntegrationValidator;

/**
 * Class ShopifyIntegrationRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class ShopifyIntegrationRepositoryEloquent extends BaseRepository implements ShopifyIntegrationRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return ShopifyIntegration::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
