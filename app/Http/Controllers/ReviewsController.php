<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ReviewsCreateRequest;
use App\Http\Requests\ReviewsUpdateRequest;
use App\Contracts\ReviewsRepository;
use App\Validators\ReviewsValidator;

/**
 * Class ReviewsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ReviewsController extends Controller
{
    /**
     * @var ReviewsRepository
     */
    protected $repository;

    /**
     * @var ReviewsValidator
     */
    protected $validator;

    /**
     * ReviewsController constructor.
     *
     * @param ReviewsRepository $repository
     * @param ReviewsValidator $validator
     */
    public function __construct(ReviewsRepository $repository, ReviewsValidator $validator)
    {
        $this->repository = $repository;
        $this->validator  = $validator;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->repository->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
        $reviews = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $reviews,
            ]);
        }

        return view('reviews.index', compact('reviews'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ReviewsCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ReviewsCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $review = $this->repository->create($request->all());

            $response = [
                'message' => 'Reviews created.',
                'data'    => $review->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {
            if ($request->wantsJson()) {
                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $review = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $review,
            ]);
        }

        return view('reviews.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $review = $this->repository->find($id);

        return view('reviews.edit', compact('review'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ReviewsUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ReviewsUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $review = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Reviews updated.',
                'data'    => $review->toArray(),
            ];

            if ($request->wantsJson()) {

                return response()->json($response);
            }

            return redirect()->back()->with('message', $response['message']);
        } catch (ValidatorException $e) {

            if ($request->wantsJson()) {

                return response()->json([
                    'error'   => true,
                    'message' => $e->getMessageBag()
                ]);
            }

            return redirect()->back()->withErrors($e->getMessageBag())->withInput();
        }
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deleted = $this->repository->delete($id);

        if (request()->wantsJson()) {

            return response()->json([
                'message' => 'Reviews deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Reviews deleted.');
    }
}
