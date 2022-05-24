<?php

namespace App\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Contracts\UserPlatformRepository;
use App\Models\UserPlatform;
use App\Validators\UserPlatformValidator;

/**
 * Class UserPlatformRepositoryEloquent.
 *
 * @package namespace App\Repositories;
 */
class UserPlatformRepositoryEloquent extends BaseRepository implements UserPlatformRepository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    public function model()
    {
        return UserPlatform::class;
    }

    

    /**
     * Boot up the repository, pushing criteria
     */
    public function boot()
    {
        $this->pushCriteria(app(RequestCriteria::class));
    }

    /**
     * @param $data
     * @return mixed
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store($data)
    {
        return $this->updateOrCreate(
            [
                'user_id' => $data->user_id,
                'provider' => $data->provider
            ],
            [
                'user_id'               => $data->user_id,
                'access_token'          => $data->access_token,
                'provider'              => $data->provider,
                'provider_id'           => $data->provider_id,
                'email'                 => $data->email,
                'provider_name'         => $data->provider_name,
                'provider_user_name'    => $data->provider_user_name,
                'provider_photo'        => $data->provider_photo,
                'meta_json'             => $data->meta_json
            ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getUserPlatforms(Request $request)
    {
        $platforms = [];
        $findPlatforms = $this->with(['userPlatformMeta'])->findByField('user_id', $request->user_id);

        foreach ($findPlatforms as $list) {
            $platforms[$list->placement_id] = $list;
        }
        return $platforms;
    }
}
