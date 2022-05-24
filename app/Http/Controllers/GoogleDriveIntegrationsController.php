<?php

namespace App\Http\Controllers;

use App\Contracts\UserPlatformRepository;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use App\Contracts\GoogleDriveIntegrationRepository;

/**
 * Class GoogleDriveIntegrationsController.
 *
 * @package namespace App\Http\Controllers;
 */
class GoogleDriveIntegrationsController extends Controller
{
    /**
     * @var GoogleDriveIntegrationRepository
     */
    protected $repository;

    /**
     * @var UserPlatformRepository
     */
    protected $userPlatformRepository;

    /**
     * @var googleDrive
     */
    protected $googleDrive;
    /**
     * GoogleDriveIntegrationsController constructor.
     *
     * @param GoogleDriveIntegrationRepository $repository
     * @param UserPlatformRepository $userPlatformRepository
     */
    public function __construct(GoogleDriveIntegrationRepository $repository, UserPlatformRepository $userPlatformRepository)
    {
        $this->repository = $repository;
        $this->userPlatformRepository = $userPlatformRepository;
        $this->googleDrive = new GoogleDriveService();
    }



    public function getSavedDataByCardId($cardId)
    {
        try {
            if ($cardId != null && isset($cardId)) {
                $records = $this->repository->findByField('card_id', $cardId);
                $responseData = array();
                foreach ($records as $currentFileRec) {
                    $responseData[] = $this->transformSavedData($currentFileRec);
                }
                $resp['status'] = true;
                $resp['data'] = $responseData;
                $resp['token_data'] = null;
                // get user access token for google drive data
                $userId = Auth()->user()->id;
                $googleDriveLoginData = $this->userPlatformRepository->findByField('user_id', $userId)->first();
                if ($googleDriveLoginData != null) {
                    $refreshedToken = $this->googleDrive->setAccessToken($googleDriveLoginData->access_token);
                    if ($refreshedToken != null) {
                        $googleDriveLoginData->access_token = json_encode($refreshedToken);
                        $this->userPlatformRepository->store($googleDriveLoginData);
                    }
                    $resp['token_data'] = $googleDriveLoginData->access_token;
                }

            }
        } catch (\Exception $e) {
            $resp['status'] = false;
            $resp['message'] = $e->getMessage();
        }

        return response()->json($resp);
    }

    /**
     * Transform data for dashboard.
     *
     * @return array
     */
    public function transformSavedData($transformSavedData)
    {
        return [
            'db_id'      => $transformSavedData['id'],
            'user_id'    => $transformSavedData['user_id'],
            'card_id'    => $transformSavedData['card_id'],
            'id'         => $transformSavedData['file_id'],
            'name'       => $transformSavedData['name'],
            'type'       => $transformSavedData['type'],
            'iconUrl'    => $transformSavedData['icon_url'],
            'url'        => $transformSavedData['url'],
        ];
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function save(Request $request)
    {;
        $resp = [
            'status'    => true,
            'message'   => 'Attachment done.',
            'card'      => null,
        ];
        $userId = Auth()->user()->id;
        $record = array();
        try {
            $data = $request->all();
            if ($data['card_id'] != null && $data['filesData'] != null) {
                $filesData = json_decode($data['filesData']);
                $currentRecord = [];
                foreach ($filesData as $file) {
                    $currentRecord['user_id'] = $userId;
                    $currentRecord['card_id'] = $data['card_id'];
                    $currentRecord['name'] = $file->name;
                    $currentRecord['file_id'] = $file->id;
                    $currentRecord['type'] = $file->type;
                    $currentRecord['icon_url'] = $file->iconUrl;
                    $currentRecord['url'] = $file->url;
                    $where = [ 'card_id' => $currentRecord['card_id'], 'file_id' => $currentRecord['file_id']];
                    $response = $this->repository->updateOrCreate($where, $currentRecord);
                    $record[] = $this->transformSavedData($response);
                }
                $resp['googleDriveIntegration'] = $record;
            }

        } catch (\Exception $e) {
            $resp['status'] = false;
            $resp['message'] = $e->getMessage();
        }
        return response()->json($resp);
    }

    public function delete($id, Request $request)
    {
        try {
            $this->repository->delete($id);
            return ['status' => 'success', 'message' => 'Deleted successfully'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
