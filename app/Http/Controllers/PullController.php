<?php

namespace App\Http\Controllers;

use App\Contracts\ShopRepository;
use App\Contracts\StickyCartRepository;
use App\Models\Shop;
use App\Services\PixelEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PullController extends Controller
{

    /**
     * @var ShopRepository
     */
    protected $repository;

    /**
     * @var StickyCartRepository
     */
    protected $stickyCartRepo;

    public function __construct(ShopRepository $repository, StickyCartRepository $stickyCartRepository)
    {
        $this->repository = $repository;
        $this->stickyCartRepo = $stickyCartRepository;
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function freeShipping($shop, Request $request)
    {
        $domain = $shop != null ? $shop : $request->get('shop');
        $shop = $this->repository->findByField('domain', $domain)->first();
        if ($shop == null) {
            return response(
                'var '.env('APP_NAME').' = "uninstalled";',
                200
            )->header('Content-Type', 'text/javascript');
        }

        $params = $this->getMoneyBarParams($shop);
        $hook = File::get(base_path() . '/resources/storefront/dist/free-shipping.min.js');
        $hook = str_replace('PAYLOAD', $params, $hook);

        return response(
            $hook,
            200
        )->header('Content-Type', 'text/javascript');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function stickyCart($shop, Request $request)
    {
        $domain = $shop != null ? $shop : $request->get('shop');
        $shop = $this->repository->findByField('domain', $domain)->first();
        $status = $shop == null ? 'uninstalled' : ($shop->billing->plan != 'enterprise' ? 'upgrade' : null);
        if ($status != null) {
            return response(
                'var '.env('APP_NAME').' = "'.$status.'";',
                200
            )->header('Content-Type', 'text/javascript');
        }

        $params = $this->getStickyCartParams($shop);
        $hook = File::get(base_path() . '/resources/storefront/dist/sticky-cart.min.js');
        $hook = str_replace('PAYLOAD', $params, $hook);

        return response(
            $hook,
            200
        )->header('Content-Type', 'text/javascript');
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function countdownTimer($shop, Request $request)
    {
        $domain = $shop != null ? $shop : $request->get('shop');
        $shop = $this->repository->findByField('domain', $domain)->first();
        $status = $shop == null ? 'uninstalled' : ($shop->billing->plan != 'enterprise' ? 'upgrade' : null);
        if ($status != null) {
            return response(
                'var '.env('APP_NAME').' = "'.$status.'";',
                200
            )->header('Content-Type', 'text/javascript');
        }

        $params = $this->getCountdownParams($shop);
        $hook = File::get(base_path() . '/resources/storefront/dist/count-down.min.js');
        $hook = str_replace('PAYLOAD', $params, $hook);

        return response(
            $hook,
            200
        )->header('Content-Type', 'text/javascript');
    }

    /**
     * load any data you want to be passed to hook
     * @param Shop $shop
     * @return string
     */
    private function getMoneyBarParams(Shop $shop)
    {
        $params = [
            'appUrl' => env('APP_URL'),
            'bars'  => $shop->moneyBars
        ];
        return json_encode($params);
    }

    /**
     * load any data you want to be passed to hook
     * @param Shop $shop
     * @return string
     */
    private function getStickyCartParams(Shop $shop)
    {
        // get active settings
        $stickyCart = $this->stickyCartRepo->findWhere(['shop_id'=> $shop->id, 'status' => '1'])->first();
        $params = array_merge([
            'appUrl' => env('APP_URL'),
            ], ($stickyCart != null ? $stickyCart->toArray() : [])
        );
        return json_encode($params);
    }

    private function getCountdownParams(Shop $shop)
    {
        $data = [
            'campaigns' => $shop->countdownTimers,
            'shop'  => $shop->domain,
            'baseURL'  => env('APP_URL')
        ];
        return json_encode($data);
    }

}
