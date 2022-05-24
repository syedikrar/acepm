<?php
/**
 * Created by PhpStorm.
 * User: Bilal
 * Date: 7/9/2018
 * Time: 10:52 AM
 */

namespace App\Services;


use App\Contracts\DefinitionRepository;
use App\Contracts\GoogleSettingRepository;
use App\Contracts\LeadRepository;
use App\Contracts\PixelSettingRepository;
use App\Contracts\ShopRepository;
use App\Events\AppUninstalled;
use App\Events\LeadCreated;
use App\Events\NotifySlack;
use App\Events\SettingUpdated;
use App\Helpers\Helper;
use App\Models\Shop;
use App\Traits\ShopifyTrait;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use stdClass;


class IntegrityService
{
    use ShopifyTrait;

    /**
     * @var ShopRepository
     */
    protected $shopRepo;

    /**
     * @var BillingService
     */
    protected $billingService;

    protected $activeTheme;

    protected $themeName;

    protected $appSignature;

    /**
     * @var WebhookService
     */
    private $webhookService;

    /**
     * IntegrityService constructor.
     *
     * @param ShopRepository $shopRepo
     * @param BillingService $billingService
     * @param WebhookService $webhookService
     */
    public function __construct(ShopRepository $shopRepo,
                                BillingService $billingService,
                                WebhookService $webhookService
    )
    {
        $this->shopRepo = $shopRepo;
        $this->billingService = $billingService;
        $this->webhookService = $webhookService;
        $this->appSignature = '{% include "'.env('APP_NAME').'" %}';
    }

    /**
     * @param $shop
     * @param $accessToken
     * @param User $user
     * @param stdClass $shopInfo
     * @return mixed
     * @throws \Exception
     */
    public function register($shop, $accessToken, User $user, stdClass $shopInfo)
    {
        // ----- delete if already exists
        $shopObj = $this->shopRepo->findByField('domain', $shop)->first();

        // ----- add a record if shop doesnt exist
        if ($shopObj == null) {
            $shopObj = $this->shopRepo->create([
                'user_id'       => $user->id,
                'domain'        => $shop,
                'token'         => $accessToken,
                'name'          => $shopInfo->name,
                'shopify_plan'  => $shopInfo->plan_name
            ]);
            $user->shops()->attach($shopObj, ['role' =>'owner']);
        }
        // ------ else just update the access token
        else {
            $shopObj->token = $accessToken;
            $shopObj->save();
        }

        $this->webhookService->injectHooks($shopObj);
        return $shopObj;
    }

    public function insepctFreePass(Shop $shop)
    {
        $sholdGet = $shop->shouldGetFreePass();
        $freePass = $shop->freePass;
        $freePass->last_checked = $sholdGet ? Carbon::now() : null;
        $freePass->save();
    }

    /**
     * Check if the integrity is in place
     * @param Request $request
     * @return array|bool
     */
    public function check(Request $request)
    {
        $shopName = $request->header('shop');
        //----------For CSUI
        $shopName = $request->get('csui-shop', $shopName);
        $shop = $this->shopRepo->findByField('domain', $shopName)->first();
        $resp = [
            'plan'      => $shop->billing->plan,
            'theme'     => null,
            'snippet'   => null,
            'checkout'  => null
        ];
        if($shop == null) return $resp;

        try {
            $this->getActiveTheme($shop , $request);
            if($request->get('csui-shop', false)){
                $shopJson = $this->getShopJson($shop);
                $resp['theme_name'] = $this->themeName;
                $resp['plan'] = $shop->billing->plan;
                $resp['shopify_plan'] = $shopJson->plan_name;
                $resp['shop_created_date'] = $shopJson->created_at;
                $resp['app_install_date'] = $shop->created_at;
                $this->syncPlan($shop, $shopJson);
            }
            $theme = $this->getActiveThemeFile($shop);
            $themeFile = $theme != null && isset($theme->asset) ? $theme->asset->value : null;
            $resp['theme'] = $themeFile != null && strpos($themeFile, $this->appSignature) != false;

            // ----- check if app snippet liquid exists && is updated
            $hookSnippet = $this->getActiveThemeFile($shop, env('APP_NAME').'.liquid', 'snippets');
            $resp['snippet'] = $hookSnippet != null;
            if ($hookSnippet != null) {
                $snippetFileTime = Carbon::createFromTimestamp(filemtime(base_path() . '/resources/storefront/ace.html'));
                $hookSnippetTime = Carbon::parse($hookSnippet->asset->updated_at);
                $hookSnippetTime->setTimezone('UTC');
                if ($hookSnippetTime->lessThan($snippetFileTime)) $resp['snippet'] = false;
            }

            if ($shop->plan == 'shopify_plus') {
                $checkoutAsset = $this->getActiveThemeFile($shop, 'checkout.liquid');
                $checkoutFile = $checkoutAsset != null && isset($checkoutAsset->asset) ? $checkoutAsset->asset->value : null;
                $resp['checkout'] = $checkoutAsset == null ? null : ($checkoutFile != null && strpos($checkoutFile, $this->appSignature) != false);
            }

            return $resp;
        } catch (\Exception $e) {
            return $resp;
        }
    }

    /**
     * inject app hook into active theme
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function fix(Request $request)
    {
        $shopName = $request->header('shop');
        //----------For CSUI
        $shopName = $request->get('csui-shop', $shopName);
        $shop = $this->shopRepo->findByField('domain', $shopName)->first();

        $resp = [
            'plan'      => ($shop != null) ? $shop->billing->plan : '',
            'theme'     => $request->get('theme', false),
            'checkout'  => $request->get('checkout', false),
            'snippet'   => $request->get('snippet', false)
        ];

        if($shop == null) return $resp;

        $this->getActiveTheme($shop, $request);
        if($request->get('csui-shop', false)){
            $resp['theme_name'] = $this->themeName;
        }
        try {
            if ($request->get('snippet', false) == false) $resp['snippet'] = $this->injectSnippet($shop);
            if ($request->get('theme', false) == false) $resp['theme'] = $this->patchThemeFile($shop);

            if ($shop->plan == 'shopify_plus') {
                $resp['checkout'] = $this->patchThemeFile($shop, 'checkout.liquid');
            }
            return $resp;

        } catch (\Exception $e) {
            return $resp;
        }
    }

    /**
     * @param $shop
     * @return mixed
     * @throws \Exception
     */
    public function getShopJson($shop)
    {
        $shopify = $this->getShopifyObj($shop);
        $resp = $shopify->call([
            'METHOD'    => 'GET',
            'URL'       => 'admin/shop.json?fields=created_at,plan_name'
        ]);
        return $resp->shop;
    }

    /**
     * @param $shop
     * @param $shopJson
     */
    public function syncPlan($shop, $shopJson){
        if($shop->plan != $shopJson->plan_name) {
            $shop->plan = $shopJson->plan_name;
            $shop->save();
        }
    }

    /**
     * @param $shop
     * @param Request $request
     * @throws \Exception
     */
    private function getActiveTheme($shop, Request $request = null)
    {
        $shopify = $this->getShopifyObj($shop);
        $csui = $request != null && $request->get('csui-shop', false);
        if ($shop->theme_id == null || $csui) {
            $resp = $shopify->call(['URL' => '/admin/themes.json', 'METHOD' => 'GET']);
            foreach($resp->themes as $key => $theme){
                if($theme->role == "main"){
                    $shop->theme_id = $theme->id;
                    $this->activeTheme = $theme->id;
                    $this->themeName = $theme->name;
                    if(!$csui) $shop->save();
                    break;
                }
            }
        } else {
            $this->activeTheme = $shop->theme_id;
        }
    }

    /**
     * get active theme's main template file
     * @param $shop
     * @param string $file
     * @param string $directory
     * @return null
     * @throws \Exception
     */
    private function getActiveThemeFile($shop, $file = 'theme.liquid', $directory = 'layout')
    {
        try {
            $shopify = $this->getShopifyObj($shop);
            $themeFile = null;

            if($this->activeTheme != null) {
                $themeFile = $shopify->call([
                    'URL' => '/admin/themes/' . $this->activeTheme . '/assets.json?asset[key]='.$directory.'/'.$file,
                    'METHOD' => 'GET'
                ]);
            }
            return $themeFile;
        } catch (\Exception $e) {
            return null;
        }
    }

    private function patchThemeFile($shop, $file = 'theme.liquid')
    {
        // ----- inject hook placeholder
        $themeFile = $this->getActiveThemeFile($shop, $file);
        if ($themeFile != null && strpos($themeFile->asset->value, $this->appSignature) == true) return true;

        if ($themeFile == null) return false;

        $gotTitle = strpos($themeFile->asset->value, '</title>') != false;
        $injection = PHP_EOL.'  '.$this->appSignature.PHP_EOL;
        if ($gotTitle) {
            $content = str_replace('</title>', '</title>'.$injection, $themeFile->asset->value);
        } else {
            $content = str_replace('</head>', $injection.'</head>', $themeFile->asset->value);
        }

        $shopify = $this->getShopifyObj($shop);
        $resp = $shopify->call([
            'METHOD'    => 'PUT',
            'URL'       => '/admin/themes/' . $this->activeTheme . '/assets.json',
            'DATA'      => [
                'asset' => [
                    'key'   => 'layout/'.$file,
                    'value' => $content,
                ]
            ]
        ]);
        return true;
    }

    /**
     * clean the active theme saved in shop table
     * @param Shop $shop
     * @throws \Exception
     */
    private function cleanTheme(Shop $shop)
    {
        $shopify = $this->getShopifyObj($shop);
        $this->cleanThemeFile($shop);

        // ----- clean checkout.liquid
        if ($shop->plan == 'shopify_plus') {
            $this->cleanThemeFile($shop, 'checkout.liquid');
        }

        // ----- send a delete call for the hook asset itself
        $hookFile = $shopify->call([
            'URL' => '/admin/themes/' . $this->activeTheme . '/assets.json?asset[key]=snippets/'.env('APP_NAME').'.liquid',
            'METHOD' => 'DELETE'
        ]);

        // ----- send a delete call for the hook asset itself
        $assetFile = $shopify->call([
            'URL' => '/admin/themes/' . $this->activeTheme . '/assets.json?asset[key]=assets/'.env('APP_NAME').'.js',
            'METHOD' => 'DELETE'
        ]);
    }

    private function cleanThemeFile($shop, $file = 'theme.liquid')
    {
        try {
            $theme = $this->getActiveThemeFile($shop, $file);
            $themeFile = isset($theme->asset) ? $theme->asset->value : null;
            $gotHook = $themeFile == null ? false :
                (strpos($themeFile, $this->appSignature) == false ? false : true);
            $shopify = $this->getShopifyObj($shop);

            // ---- remove hook placeholder
            if ($gotHook) {
                $content = str_replace(
                    $this->appSignature,
                    '',
                    $theme->asset->value
                );

                $resp = $shopify->call([
                    'METHOD'    => 'PUT',
                    'URL'       => '/admin/themes/' . $this->activeTheme . '/assets.json',
                    'DATA'      => [
                        'asset' => [
                            'key'   => 'layout/'.$file,
                            'value' => $content,
                        ]
                    ]
                ]);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }

    }

    public function uninstall($shop)
    {
        try {
            $shop = $this->shopRepo->findByField('domain', $shop)->first();
            if ($shop->billing != null) {
                $this->billingService->setShop($shop)->optout();
            }
            event(new NotifySlack($shop, 'uninstalled'));
            event(new AppUninstalled($shop));
            event(new SettingUpdated($shop));

            return response([
                'status' => true
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => false
            ], 200);
        }
    }

    public function eraseCustomer(Request $request)
    {
        return response('', 200);
    }

    public function eraseShop(Request $request)
    {
        return response('', 200);
    }

    /**
     * @param Request $request
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function shopData(Request $request)
    {
        $domain = $request->get('shop_domain');
        $shopObj = $this->shopRepo->findByField('domain', $domain)->first();
        $resp = [];
        if ($shopObj == null) {
            return $resp;
        } else {
            return response()->json($shopObj);
        }
    }

    public function billingStatus(Request $request)
    {
        $shop = $this->shopRepo->findByField('domain', $request->header('shop'))->first();
        $data = [
            'status'=> false,
            'state' => 'billing_not_set',
            'url'   => '',
            'apps'  => $shop != null ? 'https://'.$shop->domain.'/admin/apps' : ''
        ];

        if ($shop == null || $shop->billing == null) return $data;

        if ($shop->billing->status == 'pending')
        {
            $data['state'] = 'pending';
            $shopify = $this->getShopifyObj($shop);
            $charge = $shopify->call(['URL' => 'admin/recurring_application_charges/'.$shop->billing->shopify_billing_id.'.json', 'METHOD' => 'GET']);
            $chargeDetails = $charge->recurring_application_charge;

            if ($chargeDetails->status == 'declined' || $chargeDetails->status == 'expired') {
                $data['state'] = $shop->billing->status = $chargeDetails->status;
                $data['url'] = '';
                $shop->billing->save();
                return $data;
            }

            $chargeDetails = $charge->recurring_application_charge;
            $data['url'] = $chargeDetails->confirmation_url;
        } else {
            $data['state'] = $shop->billing->status;
            $data['url'] = '';
        }
        return $data;
    }

    public function cleanUninstall(Request $request, $revokeApi = true)
    {
        $shop = $this->shopRepo->findByField('domain', $request->cookie('ace-shop'))->first();

        try {
            $this->getActiveTheme($shop, $request);
            $this->cleanTheme($shop);
            // ----- revoke API Access
            if ($revokeApi) {
                $client = new Client(['base_uri' => 'https://'.$shop->domain.'/']);
                $response = $client->delete('admin/api_permissions/current.json', [
                    'headers' => [
                        'Content-Type'              => 'application/json',
                        'Accept'                    => 'application/json',
                        'Content-Length'            => '0',
                        'X-Shopify-Access-Token'    => $shop->token
                    ],
                    'curl'  => [
                        CURLOPT_RETURNTRANSFER => true
                    ]
                ]);
            }

        } catch (\Exception $e) {
            return response([
                'status' => false
            ], 500);
        }

        return response([
            'status' => true
        ], 200);

    }

    /**
     * Method to create theme liquid file
     * @param Shop $shop
     * @return bool
     */
    private function injectSnippet(Shop $shop)
    {
        try {
            $shopify = $this->getShopifyObj($shop);

            // ----- send a delete call for the hook snippet itself
            $shopify->call([
                'URL' => '/admin/themes/' . $this->activeTheme . '/assets.json?asset[key]=snippets/'.env('APP_NAME').'.liquid',
                'METHOD' => 'DELETE'
            ]);
            $themeSnippet = $this->getThemeSnippet($shop);
            $shopify->call([
                'METHOD'    => 'PUT',
                'URL'       => '/admin/themes/' . $this->activeTheme . '/assets.json',
                'DATA'      => [
                    'asset' => [
                        'key'   => 'snippets/'.env('APP_NAME').'.liquid',
                        'value' => $themeSnippet,
                    ]
                ]
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function themePublished(Request $request)
    {
        try {
            $shop = $request->input('shop');
            $shopObj = $this->shopRepo->findByField('domain', $shop)->first();

            // ----- agility check, if the active theme and the published theme are same, abort
            if ((string)$request->get('id') == $shopObj->theme_id) {
                return response([
                    'status' => false
                ], 200);
            }

            // ------ proceed with the routine if role is main
            if ($request->role == 'main') {

                // ----- clean old theme
                if ($shopObj->theme_id != null) {

                    // ----- set the old theme id in service prop, and update the one in table for agility
                    $this->getActiveTheme($shopObj, $request);
                    $shopObj->theme_id = $request->get('id');
                    $shopObj->save();

                    $this->cleanTheme($shopObj);
                }

                $this->fix($request);
            }
        } catch (\Exception $e) {
            return response([
                'status' => false
            ], 200);
        }
    }

    public function getThemeSnippet(Shop $shop)
    {
        $props = [
            'APP_VENDOR'        => env('APP_VENDOR'),
            'APP_URL'           => env('APP_URL'),
            'APP_NAME'          => env('APP_NAME_FORMATTED'),
            'FREE_SHIPPING_URL' => '',
            'STICKY_CART_URL'   => '',
            'COUNT_TIMER_URL'   => ''
        ];

        if (env('SHOPIFY_AGILITY', false)) {
            $props['FREE_SHIPPING_URL'] = route('pull-free-shipping', ['shop' => $shop->domain]);
            $props['STICKY_CART_URL'] = route('pull-sticky-cart', ['shop' => $shop->domain]);
            $props['COUNT_TIMER_URL'] = route('pull-count-down', ['shop' => $shop->domain]);
        }

        $snippetFile = File::get(base_path() . '/resources/storefront/ace.html');
        return str_replace(array_keys($props), array_values($props), $snippetFile);
    }

}
