<?php
/**
 * Created by PhpStorm.
 * User: Bilal
 * Date: 3/8/2018
 * Time: 02:23 PM
 */

namespace App\Services;


use App\Contracts\BillingRepository;
use App\Contracts\ShopRepository;
use App\Models\Shop;
use App\Traits\ShopifyTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class BouncerService
{
    use ShopifyTrait;

    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * @var BillingService
     */
    protected $billingService;

    /**
     * @var IntegrityService
     */
    protected $integrityService;


    public function __construct(
        ShopRepository $shopRepository,
        BillingService $billingService,
        IntegrityService $integrityService
    ) {
        $this->shopRepository = $shopRepository;
        $this->billingService = $billingService;
        $this->integrityService = $integrityService;
    }

    /**
     * Only proceed if billing is enable, and we find a record in billings table
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function canPass(Request $request)
    {
        $resp = [
            'status'    => true,
            'state'     => 'charged',
            'plan'      => null
        ];
        if (!env('SHOPIFY_CHARGE') || $request->user()->role == 'staff') {
            $resp['plan'] = 'enterprise';
            return $resp;
        }

        $shop = $request->header('shop');
        $affiliatePlans = ['partner_test', 'affiliate'];

        /** @var $shopObj Shop */
        $shopObj = $this->shopRepository->findByField('domain', $shop)->first();

        if ($shopObj != null) {
            $freePass = $shopObj->freePass;
            $affiliate = in_array($shopObj->shopify_plan, $affiliatePlans) && env('SHOPIFY_SUPPORT_AFFILIATES', false);

            if (!$affiliate || env('SHOPIFY_CHARGE')) {
                if ($freePass != null && $freePass->active == false) {
                    return $this->freePassExpired($request);
                }

                if (env('SHOPIFY_CHARGE') && $shopObj->billing != null) {
                    $resp['plan'] = $shopObj->billing->plan;
                    $this->billingService->setShop($shopObj);
                    if ($shopObj->billing->status != 'active') {
                        return $this->integrityService->billingStatus($request);
                    }
                }

                // ----- if user didnt select billing plan ...
                if (env('SHOPIFY_BILLING_PLANS') &&
                    $shopObj->billing == null &&
                    !$affiliate &&
                    !$shopObj->free_pass) {
                    $resp['status'] = false;
                    $resp['state'] = Carbon::now()->diffInMinutes($shopObj->created_at) > 10 ? 'billing_not_set' : 'choose_plan';
                    return $resp;
                }
            }
        }

        return $resp;
    }

    /**
     * @param Request $request
     * @return array
     */
    private function freePassExpired(Request $request)
    {
        $resp = [
            'status'    => false,
            'state'     => 'free_pass_expired'
        ];

        $uri = $request->getRequestUri();
        if (Str::startsWith($uri, '/pull')) {
            //block to cover pull js requests
        } else {
            return $resp;
        }
    }

}
