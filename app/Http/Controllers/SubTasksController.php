<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\SubTaskCreateRequest;
use App\Http\Requests\SubTaskUpdateRequest;
use App\Contracts\SubTaskRepository;
use App\Validators\SubTaskValidator;

/**
 * Class SubTasksController.
 *
 * @package namespace App\Http\Controllers;
 */
class SubTasksController extends Controller
{
    /**
     * @var SubTaskRepository
     */
    protected $repository;

    /**
     * SubTasksController constructor.
     *
     * @param SubTaskRepository $repository
     */
    public function __construct(SubTaskRepository $repository)
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
            'message'   => 'Sub Task Updated.',
            'entity'    => null
        ];

        try {
            $data = $request->all();
            $id = [ 'id' => $data['id']];
            unset($data['id']);
            $resp['entity'] = $this->repository->updateOrCreate($id, $data);
            $resp['entity']->load('assignee');

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
