<?php

namespace App\Http\Controllers;

use App\Contracts\FieldTypeRepository;
use App\Contracts\ShopRepository;
use App\Services\IntegrityService;
use App\Traits\ShopifyTrait;
use Illuminate\Http\Request;

class IntegrityController extends Controller
{
    use ShopifyTrait;
    /**
     * @var ShopRepositoryEloquent
     */
    protected $repository;

    /**
     * @var IntegrityService
     */
    protected $integrity;

    /**
     * @var \App\Contracts\FieldTypeRepository
     */
    protected $fieldTypeRepository;

    public function __construct(
        ShopRepository $repository,
        IntegrityService $integrityService,
        FieldTypeRepository $fieldTypeRepository
    ) {
        $this->repository = $repository;
        $this->integrity = $integrityService;
        $this->fieldTypeRepository = $fieldTypeRepository;
    }

    /**
     * Check if the integrity is in place
     * @param Request $request
     * @return bool
     * @throws \Exception
     */
    public function check(Request $request)
    {
        return response()->json($this->integrity->check($request));
    }

    /**
     * Fix integrity check
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function fix(Request $request)
    {
        $resp = $this->integrity->fix($request);
        return response()->json($resp);
    }

    /**
     * Hook method to remove shop from shops table
     * @param Request $request
     * @return void
     */
    public function uninstall(Request $request)
    {
        $shop = $request->input('myshopify_domain');
        return $this->integrity->uninstall($shop);
    }

    /**
     * GDPR hook method to remove customer data from App
     * @param Request $request
     * @return void
     */
    public function eraseCustomer(Request $request)
    {
        return $this->integrity->eraseCustomer($request);
    }

    /**
     * GDPR hook method to remove shop data from App
     * @param Request $request
     * @return void
     */
    public function eraseShop(Request $request)
    {
        return $this->integrity->eraseShop($request);
    }

    /**
     * GDPR hook method to get shop data
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function shopData(Request $request)
    {
        return $this->integrity->shopData($request);
    }

    public function getAssetURL(Request $request)
    {
        $shop = $request->get('shop');
        return response()->json($this->integrity->getAssetURL($shop));
    }

    public function shopInfo(Request $request)
    {
        $shopName = $request->header('shop');
        //----------For CSUI
        $shopName = $request->get('csui-shop', $shopName);
        $shop = $this->repository->findByField('domain', $shopName)->first();

        $shopify = $this->getShopifyObj($shop);
        $shopInfo = $shopify->call(['URL' => 'shop.json', 'METHOD' => 'GET']);

        return response()->json($shopInfo->shop);
    }

    public function fieldTypes()
    {
        return $this->fieldTypeRepository->findByField('enabled', true);
    }

    public function fieldType($slug)
    {
        return $this->fieldTypeRepository->findByField('slug', $slug)->first();
    }

}
