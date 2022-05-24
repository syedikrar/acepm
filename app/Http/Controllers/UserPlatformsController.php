<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use App\Contracts\UserPlatformRepository;
use Illuminate\Database\Eloquent\Collection;

/**
 * Class UserPlatformsController.
 *
 * @package namespace App\Http\Controllers;
 */
class UserPlatformsController extends Controller
{
    /**
     * @var UserPlatformRepository
     */
    protected $repository;

    /**
     * @var googleDrive
     */
    protected $googleDrive;

    /**
     * UserPlatformsController constructor.
     *
     * @param UserPlatformRepository $repository
     */
    public function __construct(UserPlatformRepository $repository)
    {
        $this->repository = $repository;
        $this->googleDrive = new GoogleDriveService();
    }

    public function redirectToGoogleProvider()
    {   $userId = Auth()->user()->id;
        return response()->json([
            'url' => $this->googleDrive->getAuthUrl($userId)
        ]);

    }

    public function handleProviderGoogleCallback(Request $request)
    {
        $userId = $request->input('state');

        $token = $this->googleDrive->authenticateToken($request->input('code'));
        // Get User Info Object
        $getUserInfo = $this->googleDrive->getUserInfo($token);

        $metaObject = $this->createMetaObject($getUserInfo, 'google-drive', $userId, $token );
        $this->repository->store($metaObject);
        $response = array('accessToken' => json_encode($token));
        return view('callback',  $response);
    }
    /**
     * @param $payload
     * @param $provider
     * @param $user_id
     * @param null $token
     * @return Collection
     */
    private function createMetaObject($payload, $provider, $user_id, $token)
    {
        $metaObject = new Collection();
        if ($provider == 'google-drive') {
            $metaObject->user_id = $user_id;
            $metaObject->email = $payload->email;
            $metaObject->provider = 'google';
            $metaObject->access_token = json_encode($token);;
            $metaObject->provider_id = $payload->id;
            $metaObject->provider_name = $payload->name;
            $metaObject->provider_user_name = $payload->givenName;
            $metaObject->provider_photo =$payload->picture;
            $metaObject->meta_json = json_encode($payload);
            return $metaObject;
        }
    }
}
