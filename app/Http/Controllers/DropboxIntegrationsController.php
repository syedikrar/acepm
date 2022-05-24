<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\DropboxIntegrationRepository;
use App\Validators\DropboxIntegrationValidator;

/**
 * Class DropboxIntegrationsController.
 *
 * @package namespace App\Http\Controllers;
 */
class DropboxIntegrationsController extends Controller
{
    /**
     * @var DropboxIntegrationRepository
     */
    protected $repository;

    /**
     * DropboxIntegrationsController constructor.
     *
     * @param DropboxIntegrationRepository $repository
     */
    public function __construct(DropboxIntegrationRepository $repository)
    {
        $this->repository = $repository;
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
                    $currentRecord['icon_url'] = $file->icon;
                    $currentRecord['url'] = $file->link;
                    $where = [ 'card_id' => $currentRecord['card_id'], 'file_id' => $currentRecord['file_id']];
                    $response = $this->repository->updateOrCreate($where, $currentRecord);
                    $record[] = $this->transformSavedData($response);
                }
                $resp['dropboxIntegration'] = $record;
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
