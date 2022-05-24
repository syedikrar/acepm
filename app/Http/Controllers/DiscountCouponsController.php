<?php

namespace App\Http\Controllers;

use App\Contracts\ShopRepository;
use App\Services\BillingService;
use Illuminate\Http\Request;

use App\Contracts\DiscountCouponRepository;

/**
 * Class DiscountCouponsController.
 *
 * @package namespace App\Http\Controllers;
 */
class DiscountCouponsController extends Controller
{
    /**
     * @var DiscountCouponRepository
     */
    protected $repository;
    /**
     * @var BillingService
     */
    protected $billingService;
    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * DiscountCouponsController constructor.
     *
     * @param DiscountCouponRepository $repository
     * @param BillingService $billingService
     * @param ShopRepository $shopRepository
     */
    public function __construct(DiscountCouponRepository $repository,
                                BillingService $billingService,
                                ShopRepository $shopRepository)
    {
        $this->repository = $repository;
        $this->billingService = $billingService;
        $this->shopRepository = $shopRepository;
    }

    public function get(Request $request)
    {
        return response()->json([
            'discount-coupons' => $this->repository->orderBy('created_at', 'Desc')->all()
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save(Request $request)
    {
        $result = $this->repository->persist($request);
        return response()->json($result);
    }

    public function check(Request $request)
    {
        $result = $this->repository->check($request);
        return response()->json(
            ['coupon' => $result]
        );
    }

    public function delete($id)
    {
        $this->repository->delete($id);
        return response()->json([
            'status' => true
        ]);
    }
}
