<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\CardCreateRequest;
use App\Http\Requests\CardUpdateRequest;
use App\Contracts\CardRepository;
use App\Validators\CardValidator;

/**
 * Class CardsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CardsController extends Controller
{
    /**
     * @var CardRepository
     */
    protected $repository;

    /**
     * CardsController constructor.
     *
     * @param CardRepository $repository
     */
    public function __construct(CardRepository $repository)
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
            'message'   => 'Card Updated.',
            'card'      => null
        ];

        try {
            $data = $request->all();
            $id = [ 'id' => $data['id']];
            unset($data['id']);
            $resp['card'] = $this->repository->updateOrCreate($id, $data);

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
            return ['status' => 'success', 'message' => 'Deleted successfully.'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function get($id)
    {
        return $this->repository->find($id);
    }
}
