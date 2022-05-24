<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ShopUserCreateRequest;
use App\Http\Requests\ShopUserUpdateRequest;
use App\Contracts\ShopUserRepository;
use App\Validators\ShopUserValidator;

/**
 * Class ShopUsersController.
 *
 * @package namespace App\Http\Controllers;
 */
class ShopUsersController extends Controller
{
    /**
     * @var ShopUserRepository
     */
    protected $repository;

    /**
     * @var ShopUserValidator
     */
    protected $validator;

    /**
     * ShopUsersController constructor.
     *
     * @param ShopUserRepository $repository
     * @param ShopUserValidator $validator
     */
    public function __construct(ShopUserRepository $repository, ShopUserValidator $validator)
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
        $shopUsers = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $shopUsers,
            ]);
        }

        return view('shopUsers.index', compact('shopUsers'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ShopUserCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ShopUserCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $shopUser = $this->repository->create($request->all());

            $response = [
                'message' => 'ShopUser created.',
                'data'    => $shopUser->toArray(),
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
        $shopUser = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $shopUser,
            ]);
        }

        return view('shopUsers.show', compact('shopUser'));
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
        $shopUser = $this->repository->find($id);

        return view('shopUsers.edit', compact('shopUser'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ShopUserUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ShopUserUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $shopUser = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'ShopUser updated.',
                'data'    => $shopUser->toArray(),
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
                'message' => 'ShopUser deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'ShopUser deleted.');
    }
}
