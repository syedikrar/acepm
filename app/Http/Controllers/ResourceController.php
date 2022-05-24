<?php

namespace App\Http\Controllers;

use App\Contracts\ShopRepository;
use App\Models\Shop;
use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Env;
use PHPShopify\ShopifySDK;
use Prettus\Validator\Contracts\ValidatorInterface;
use Prettus\Validator\Exceptions\ValidatorException;
use App\Http\Requests\OfferCreateRequest;
use App\Http\Requests\OfferUpdateRequest;
use App\Contracts\OfferRepository;
use App\Validators\OfferValidator;

/**
 * Class OffersController.
 *
 * @package namespace App\Http\Controllers;
 */
class ResourceController extends Controller
{
    /**
     * @var ShopRepository
     */
    protected $shopRepository;

    /**
     * ResourceController constructor.
     *
     * @param ShopRepository $shopRepository
     */
    public function __construct(ShopRepository $shopRepository)
    {
        $this->shopRepository = $shopRepository;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getProducts(Request $request)
    {
        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;
        $shop = $this->shopRepository->findByField('domain', $shop)->first();
        $shopify = $this->getShopifyObj($shop, '');

        $generalData = array();
        $productData = array();
        if ($request->search == null || $request->search == 'null') {
            $getAll = $shopify->call(['URL' => 'admin/products.json?fields=handle,id,title,variants,options,image,images,&title=' . '', 'METHOD' => 'GET']);
            $generalData = $getAll->products;
        }

        if ($request->search != null && $request->search != 'null'){
            if (strpos($request->search, 'ids_') !== false) {

                $shopifyResponse = $shopify->call(['URL' => 'admin/products.json?fields=handle,id,title,variants,options,image,images,&ids='. ($request->search == 'null' ? '' : urlencode(rtrim(str_replace("ids_", '', $request->search), ','))) , 'METHOD' => 'GET']);
            } else {
                // create shopify api object for graph calls
                $name = $shop instanceof Shop ? $shop->name : $shop;
                $token = $shop instanceof Shop ? $shop->access_token : null;
                $config = array(
                    'ShopUrl' => $name,
                    'AccessToken' => $token,
                );
                $shopifyGraph = new ShopifySDK($config);
                $graphQL = <<<Query
            query	{
                products(first:10, query: "title:$request->search*")	{
                pageInfo	{
                        hasNextPage
                        hasPreviousPage
                }
                edges	{
                        node	{
                                id
                        }
                }
            }
    }
Query;
                $productIds = '';
                $shopifyResult = $shopifyGraph->GraphQL->post($graphQL);
                // get data and inside data get products and loop it.
                if (!empty($shopifyResult['data'])) {
                    $shopifyProducts = $shopifyResult['data']['products'];
                    if (!empty($shopifyProducts['edges'])) {
                        $productsData = $shopifyProducts['edges'];
                        foreach ($productsData as $key => $currentProductId){
                            if (!empty($currentProductId['node']['id']) && !is_null($currentProductId['node']['id'])) {
                                $productId = $currentProductId['node']['id'];
                                $productIds .= preg_replace('/[^0-9]/', '', $productId) . ',';
                            }
                        }
                    }
                }
                $shopifyResponse = $shopify->call(['URL' => 'admin/products.json?fields=handle,id,title,variants,options,image,images,&ids='. ($productIds == 'null' ? '' : $productIds) , 'METHOD' => 'GET']);
                //$shopifyResponse = $shopify->call(['URL' => 'admin/products.json?fields=handle,id,title,variants,options,image,images,&title='. ($request->search == 'null' ? '' : urlencode($request->search)) , 'METHOD' => 'GET']);
            }
            $productData = $shopifyResponse->products;
        }

        $result = array_merge($productData, $generalData);
        return response()->json([
            'message' => 'Links fetched successfully',
            'status' => true,
            'data' => $result
        ]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getOrders(Request $request)
    {

        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;


        $shop = $this->shopRepository->findByField('domain', $shop)->first();
        $shopify = $this->getShopifyObj($shop, '');

        $generalData = array();
        $ordersData = array();
        if ($request->search == null || $request->search == 'null') {
            $getAll = $shopify->call(['URL' => '/admin/api/' . env('SHOPIFY_API_VERSION') .'/orders.json?status=any' . '', 'METHOD' => 'GET']);
            $generalData = $getAll->orders;
        }

        if ($request->search != null && $request->search != 'null'){
            if (strpos($request->search, 'ids_') !== false) {

                $shopifyResponse = $shopify->call(['URL' => 'admin/orders.json?ids='. ($request->search == 'null' ? '' : urlencode(rtrim(str_replace("ids_", '', $request->search), ','))) , 'METHOD' => 'GET']);
            } else {
//                /*// create shopify api object for graph calls
//                $name = $shop instanceof Shop ? $shop->name : $shop;
//                $token = $shop instanceof Shop ? $shop->access_token : null;
//                $config = array(
//                    'ShopUrl' => $name,
//                    'AccessToken' => $token,
//                );
//                $shopifyGraph = new ShopifySDK($config);
//                $graphQL = <<<Query
//            query	{
//                products(first:10, query: "title:$request->search*")	{
//                pageInfo	{
//                        hasNextPage
//                        hasPreviousPage
//                }
//                edges	{
//                        node	{
//                                id
//                        }
//                }
//            }
//    }
//Query;
//                $productIds = '';
//                $shopifyResult = $shopifyGraph->GraphQL->post($graphQL);
//                // get data and inside data get products and loop it.
//                if (!empty($shopifyResult['data'])) {
//                    $shopifyProducts = $shopifyResult['data']['products'];
//                    if (!empty($shopifyProducts['edges'])) {
//                        $productsData = $shopifyProducts['edges'];
//                        foreach ($productsData as $key => $currentProductId){
//                            if (!empty($currentProductId['node']['id']) && !is_null($currentProductId['node']['id'])) {
//                                $productId = $currentProductId['node']['id'];
//                                $productIds .= preg_replace('/[^0-9]/', '', $productId) . ',';
//                            }
//                        }
//                    }
//                }
//                $shopifyResponse = $shopify->call(['URL' => 'admin/products.json?fields=handle,id,title,variants,options,image,images,&ids='. ($productIds == 'null' ? '' : $productIds) , 'METHOD' => 'GET']);*/
                //$shopifyResponse = $shopify->call(['URL' => 'admin/products.json?fields=handle,id,title,variants,options,image,images,&title='. ($request->search == 'null' ? '' : urlencode($request->search)) , 'METHOD' => 'GET']);
            }
            $ordersData = $shopifyResponse->orders;
        }
        $result = array_merge($ordersData, $generalData);
        return response()->json([
            'message' => 'Links fetched successfully',
            'status' => true,
            'data' => $result
        ]);
    }
}
