<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\CommentCreateRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Contracts\CommentRepository;
use App\Validators\CommentValidator;

/**
 * Class CommentsController.
 *
 * @package namespace App\Http\Controllers;
 */
class CommentsController extends Controller
{
    /**
     * @var CommentRepository
     */
    protected $repository;

    /**
     * SubTasksController constructor.
     *
     * @param CommentRepository $repository
     */
    public function __construct(CommentRepository $repository)
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
            'message'   => 'Comment Updated.',
            'entity'    => null
        ];

        try {
            $data = $request->all();
            $id = [ 'id' => $data['id']];
            unset($data['id']);
            $comment = $this->repository->updateOrCreate($id, $data);
            $resp['entity'] = $this->repository->with('author')->find($comment['id']);

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
