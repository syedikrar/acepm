<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\CardAttachmentRepository;
use App\Models\CardAttachment;
use App\Validators\CardAttachmentValidator;

/**
 * Class CardAttachmentRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class CardAttachmentRepositoryEloquent extends BaseRepository implements CardAttachmentRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return CardAttachment::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }
    
}
