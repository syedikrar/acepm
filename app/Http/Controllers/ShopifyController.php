<?php

namespace App\Http\Controllers;

use App\Contracts\BillingRepository;
use App\Contracts\ShopRepository;
use App\Events\AppInstalled;
use App\Events\NotifySlack;
use App\Events\ProductCreated;
use App\Events\SettingUpdated;
use App\Models\Shop;
use App\Services\BillingService;
use App\Services\IntegrityService;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use ShopSettingsTableSeeder;
use Tymon\JWTAuth\JWTAuth;


class ShopifyController extends Controller
{
    use AuthenticatesUsers;
    /**
     * @var \App\Contracts\ShopRepository
     */
    protected $repository;

    /**
     * @var IntegrityService
     */
    protected $integrity;

    /**
     * @var BillingService
     */
    protected $billingService;
    /**
     * @var BillingRepository
     */
    private $billingRepository;

    public function __construct(ShopRepository $shopRepository,
                                IntegrityService $integrityService,
                                BillingService $billingService,
                                BillingRepository $billingRepository)
    {
        $this->repository = $shopRepository;
        $this->integrity = $integrityService;
        $this->billingRepository = $billingRepository;
        $this->billingService = $billingService;
    }

    /**
     * helper method to perform shopify auth
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function install(Request $request)
    {
        $shop = $request->has('shop') ? $request->get('shop') : null;
        $shopObj = $this->repository->with('owner')->findByField('domain', $shop)->first();

        // ----- check if the shop is already set, then use it
        if ($shopObj != null) {
            $user = $shopObj->owner->first();
            return $this->redirectWithAuthCookie($user, $shopObj);

        } elseif ($shop != null) {
            $shopify = $this->getShopifyObj($shop, '');
            $permissionURL = $shopify->installURL([
                'permissions' => config('shopify.permissions'),
                'redirect' => route('shopify.auth-callback')
            ]);
            return redirect($permissionURL);
        }
    }

    /**
     * helper method to perform shopify auth
     *
     * @param                          $shop
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function installPath($shop, Request $request)
    {
        $shopify = $this->getShopifyObj($shop, '');
        $permissionURL = $shopify->installURL([
            'permissions'   => config('shopify.permissions'),
            'redirect'      => route('shopify.auth-callback')
        ]);

        return response()->json(['url' => $permissionURL]);
    }

    /**
     * action for shopify callback URL, to store access token and login the user.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return void
     * @throws \Exception
     */
    public function authCallback(Request $request)
    {
        if (isset($_GET['code'])) {
            $shop = $_GET['shop'];
            $code = $_GET['code'];

            $shopify = $this->getShopifyObj($shop, '');
            $accessToken = $shopify->getAccessToken($code);
            $shopify->setup(['ACCESS_TOKEN' => $accessToken]);

            try {
                $shopInfo = $shopify->call(['URL' => 'shop.json', 'METHOD' => 'GET']);
                $user = $this->repository->createOwner($shopInfo->shop);
                $shop = $this->integrity->register($shop, $accessToken, $user, $shopInfo->shop);
                $result = $this->billingService->setShop($shop)->inspect();

                if ($result['redirect'] != null) {
                    return redirect($result['redirect']);
                } elseif($result['target'] == 'loggedin') {
                    return $this->redirectWithAuthCookie($user, $shop);
                }

            } catch (\Exception $e) {
                echo $e->getMessage();
                exit;
            }
        }
    }

    public function approve(User $user) {
        if($user) {
            $user->approved_at = Carbon::now();
            $user->save();
        }

        return redirect("/");
    }

    public function chargeCallback(Request $request)
    {
        if ($request->has('charge_id')) {
            $billing = $this
                ->billingRepository
                ->findByField('shopify_billing_id', $request->get('charge_id'))
                ->first();

            $shop = $billing->shop;
            $approved = $this->billingService->approveCharge($request);
            event(new SettingUpdated($shop));
            event(new NotifySlack($shop, 'installed'));
            if ($approved) {
                return $this->redirectWithAuthCookie($shop->owner->first(), $shop);
            }
        } else {
            return '';
        }
    }

    public function linkedShops(Request $request)
    {
        return response()->json($this->repository->with('users')->all());
    }

    public function userShops(Request $request)
    {
        return response()->json($request->user()->shops);
    }

    public function cleanUninstall(Request $request)
    {
        return $this->integrity->cleanUninstall($request);
    }

    /**
     * @param $plan
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function planSelected($plan, Request $request)
    {
        $shop = $request->header('shop');
        $shop = $this->repository->findByField('domain', $shop)->first();
        if ($shop->billing != null) {
            $shop->billing->status = 'cancelled';
            $shop->billing->save();
            $shop->billing->delete();
        }
        $this->billingService->setShop($shop);
        $this->billingService->cleanup();
        return response()->json($this->billingService->enroll($plan, $request->get('couponCode')));
    }

    /**
     * @param User $user
     * @param Shop $shop
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    private function redirectWithAuthCookie(User $user, Shop $shop)
    {

        $token = auth()->login($user);
        return redirect('/')->withCookies([
            cookie('ace-token', $token, 160,'/',null,false,false),
            cookie('ace-shop', $shop->domain, 160,'/',null,false,false),
            cookie('ace-shop-id', $shop->id, 160,'/',null,false,false)
        ]);
    }
    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getCollections(Request $request, $shop_name, $product_id)
    {
        $shopObj = $this->repository->findByField('name', $shop_name);

        // ----- check if the shop is already set, then use it
        if ($shopObj->count() > 0) {

            $shopObj = $shopObj[0];
            $shop = $shopObj['name'];
            $access_token = $shopObj['access_token'];
            /**
             * @var API $shopify
             */
            $shopify = \App::make('ShopifyAPI', [
                'API_KEY' => env('SHOPIFY_API_KEY'),
                'API_SECRET' => env('SHOPIFY_API_SECRET'),
                'SHOP_DOMAIN' => $shop,
                'ACCESS_TOKEN' => $access_token
            ]);

            // Gets a list of products
            $custom_collections = $shopify->call([
                'METHOD' => 'GET',
                'URL' => "/admin/custom_collections.json?product_id=$product_id"
            ]);

            $smart_collections = $shopify->call([
                'METHOD' => 'GET',
                'URL' => "/admin/smart_collections.json?product_id=$product_id"
            ]);

            $smart_collections = $smart_collections->smart_collections;

            $custom_collections = $custom_collections->custom_collections;

            $collections = array_merge($smart_collections, $custom_collections);

            return response($collections)
                ->header('Access-Control-Allow-Origin', '*')
                ->header('Access-Control-Allow-Methods', 'GET');
        }
    }

    /**
     * helper method to perform shopify auth
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getAllProducts(Request $request)
    {
        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;
        $shopObj = $this->repository->findByField('domain', $shop);

        // ----- check if the shop is already set, then use it
        if ($shopObj->count() > 0) {

            $shopObj = $shopObj[0];
            $shop = $shopObj['domain'];
            $access_token = $shopObj['token'];
            $shopify = $this->getShopifyObj($shop, $access_token);

            // Gets a list of products
            $result = $shopify->call([
                'METHOD' => 'GET',
                'URL' => '/admin/products.json'
            ]);
            $products = $result->products;

            return $products;

        }

    }

    /**
     * helper method to perform shopify auth
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Exception
     */
    public function getProduct($id, Request $request)
    {
        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;
        $shopObj = $this->repository->findByField('domain', $shop);

        // ----- check if the shop is already set, then use it
        if ($shopObj->count() > 0) {

            $shopObj = $shopObj[0];
            $shop = $shopObj['domain'];
            $access_token = $shopObj['token'];
            $shopify = $this->getShopifyObj($shop, $access_token);

            // Gets a list of products
            $result = $shopify->call([
                'METHOD' => 'GET',
                'URL' => '/admin/products/'.$id.'.json'
            ]);
            $product = $result->product;

            return json_encode($product);

        }

    }

    /**
     * helper method to perform shopify auth
     * @param $title
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getFilteredProducts($title, Request $request)
    {
        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;
        $shopObj = $this->repository->findByField('domain', $shop)->first();

        // ----- check if the shop is already set, then use it
        if (empty($shopObj)) return [];

        $query = "query	{
                    products(first:10, query: \"title:".$title."*\")	{
                        edges	{
                            node	{
                                id
                            }
                        }
                    }
                }";
        //----------- Get shopify object for GraphQl Queries
        $shopify = $this->getShopifyObj($shopObj, $shopObj->token, 'graph');
        $result = $shopify->GraphQL->post($query);

        //----------- String that will be populated with product IDs for rest api call
        $title = '';
        if(!empty($result) && !empty($result['data']) && !empty($result['data']['products'])
            && !empty($result['data']['products']['edges'])){
            $productList = $result['data']['products']['edges'];
            foreach ($productList as $key => $product){
                if (!empty($product['node']) && !empty($product['node']['id'])) {
                    $graphId = $product['node']['id'];
                    $title .= preg_replace('/[^0-9]/', '', $graphId) . ',';
                }
            }
        }
        $options = [
            'limit'     => 10,
            'fields'    => 'id,title,variants,images,image,handle',
            'ids'     => $title
        ];

        //----------- Get shopify object for Rest API Calls
        $shopify = $this->getShopifyObj($shopObj, $shopObj->token);
        $resp = $shopify->call([
            'URL' => '/admin/products.json?'.urldecode(http_build_query($options)),
            'METHOD' => 'GET'
        ]);

        return $resp->products;

    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getAllCollections(Request $request)
    {
        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;
        $shopObj = $this->repository->findByField('domain', $shop);

        // ----- check if the shop is already set, then use it
        if ($shopObj->count() > 0) {

            $shopObj = $shopObj[0];
            $shop = $shopObj['domain'];
            $access_token = $shopObj['token'];
            /**
             * @var API $shopify
             */
            $shopify = \App::make('ShopifyAPI', [
                'API_KEY' => env('SHOPIFY_API_KEY'),
                'API_SECRET' => env('SHOPIFY_API_SECRET'),
                'SHOP_DOMAIN' => $shop,
                'ACCESS_TOKEN' => $access_token
            ]);

            // Gets a list of collections
            $smart_collections = $shopify->call([
                'METHOD' => 'GET',
                'URL' => "/admin/smart_collections.json"
            ]);
            $custom_collections = $shopify->call([
                'METHOD' => 'GET',
                'URL' => "/admin/custom_collections.json"
            ]);

            $collections = array_merge($smart_collections->smart_collections, $custom_collections->custom_collections);

            return response($collections)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getCollection($id, Request $request)
    {
        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;
        $shopObj = $this->repository->findByField('domain', $shop);

        // ----- check if the shop is already set, then use it
        if ($shopObj->count() > 0) {

            $shopObj = $shopObj[0];
            $shop = $shopObj['domain'];
            $access_token = $shopObj['token'];
            /**
             * @var API $shopify
             */
            $shopify = \App::make('ShopifyAPI', [
                'API_KEY' => env('SHOPIFY_API_KEY'),
                'API_SECRET' => env('SHOPIFY_API_SECRET'),
                'SHOP_DOMAIN' => $shop,
                'ACCESS_TOKEN' => $access_token
            ]);

            $result = $shopify->call([
                'METHOD' => 'GET',
                'URL' => "/admin/collections/" . $id . ".json"
            ]);

            $collection = $result->collection;

            return json_encode($collection);

        }
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function getFilteredCollections($title, Request $request)
    {
        $shop = $request->cookie('ace-shop') ? $request->cookie('ace-shop') : null;
        $shopObj = $this->repository->findByField('domain', $shop);

        // ----- check if the shop is already set, then use it
        if ($shopObj->count() > 0) {

            $shopObj = $shopObj[0];
            $shop = $shopObj['domain'];
            $access_token = $shopObj['token'];
            /**
             * @var API $shopify
             */
            $shopify = \App::make('ShopifyAPI', [
                'API_KEY' => env('SHOPIFY_API_KEY'),
                'API_SECRET' => env('SHOPIFY_API_SECRET'),
                'SHOP_DOMAIN' => $shop,
                'ACCESS_TOKEN' => $access_token
            ]);

            // Gets a list of collections
            $smart_collections = $shopify->call([
                'METHOD' => 'GET',
                'URL' => "/admin/smart_collections.json?title=".urlencode($title)
            ]);
            $custom_collections = $shopify->call([
                'METHOD' => 'GET',
                'URL' => "/admin/custom_collections.json?title=".urlencode($title)
            ]);

            $collections = array_merge($smart_collections->smart_collections, $custom_collections->custom_collections);

            return response($collections)
                ->header('Access-Control-Allow-Origin', '*');
        }
    }

}
