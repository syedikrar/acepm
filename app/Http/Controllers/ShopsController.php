<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\ShopCreateRequest;
use App\Http\Requests\ShopUpdateRequest;
use App\Contracts\ShopRepository;
use App\Validators\ShopValidator;

/**
 * Class ShopsController.
 *
 * @package namespace App\Http\Controllers;
 */
class ShopsController extends Controller
{
    /**
     * @var ShopRepository
     */
    protected $repository;

    /**
     * @var ShopValidator
     */
    protected $validator;

    /**
     * ShopsController constructor.
     *
     * @param ShopRepository $repository
     * @param ShopValidator $validator
     */
    public function __construct(ShopRepository $repository, ShopValidator $validator)
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
        $shops = $this->repository->all();

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $shops,
            ]);
        }

        return view('shops.index', compact('shops'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ShopCreateRequest $request
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(ShopCreateRequest $request)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_CREATE);

            $shop = $this->repository->create($request->all());

            $response = [
                'message' => 'Shop created.',
                'data'    => $shop->toArray(),
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
        $shop = $this->repository->find($id);

        if (request()->wantsJson()) {

            return response()->json([
                'data' => $shop,
            ]);
        }

        return view('shops.show', compact('shop'));
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
        $shop = $this->repository->find($id);

        return view('shops.edit', compact('shop'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ShopUpdateRequest $request
     * @param  string            $id
     *
     * @return Response
     *
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function update(ShopUpdateRequest $request, $id)
    {
        try {

            $this->validator->with($request->all())->passesOrFail(ValidatorInterface::RULE_UPDATE);

            $shop = $this->repository->update($request->all(), $id);

            $response = [
                'message' => 'Shop updated.',
                'data'    => $shop->toArray(),
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
                'message' => 'Shop deleted.',
                'deleted' => $deleted,
            ]);
        }

        return redirect()->back()->with('message', 'Shop deleted.');
    }
}
