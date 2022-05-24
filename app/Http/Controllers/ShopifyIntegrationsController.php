<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ShopifyIntegrationCreateRequest;
use App\Http\Requests\ShopifyIntegrationUpdateRequest;
use App\Contracts\ShopifyIntegrationRepository;
use App\Validators\ShopifyIntegrationValidator;

/**
 * Class ShopifyIntegrationsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ShopifyIntegrationsController extends Controller
{
    /**
     * @var ShopifyIntegrationRepository
     */
    protected $repository;

    /**
     * ShopifyIntegrationsController constructor.
     *
     * @param ShopifyIntegrationRepository $repository

     */
    public function __construct(ShopifyIntegrationRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request $request
     *
     * @return void
     */
    public function save(Request $request)
    {
        $resp = [
            'status'    => true,
            'message'   => 'Attachment done.',
            'card'      => null,
            'newly_created' => false
        ];

        try {
            $data = $request->all();
            $where = [ 'resource_id' => $data['resource_id'], 'resource_type' => $data['resource_type'], 'card_id' => $data['card_id']];
            //$id = [ 'id' => $data['id']];
            unset($data['id']);
            $response = $this->repository->updateOrCreate($where, $data);
            $resp['shopifyIntegration'] = $response;
            if ($response->wasRecentlyCreated) {
                $resp['newly_created'] = true;
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
