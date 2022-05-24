<?php
/**
 * Created by PhpStorm.
 * User: Bilal
 * Date: 7/9/2018
 * Time: 10:52 AM
 */

namespace App\Services;


use App\Contracts\DefinitionRepository;
use App\Contracts\ShopRepository;
use App\Helpers\Helper;
use App\Models\Shop;
use App\Traits\ShopifyTrait;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use stdClass;
use Illuminate\Support\Facades\Log;
use function Symfony\Component\Console\Tests\Command\createClosure;

class WebhookService
{
    use ShopifyTrait;

    /**
     * @var ShopRepository
     */
    protected $shopRepo;

    /**
     * IntegrityService constructor.
     *
     * @param ShopRepository         $shopRepo
     */
    public function __construct(ShopRepository $shopRepo)
    {
        $this->shopRepo = $shopRepo;
    }

    /**
     * initialize hooks on shop when new install
     * @param $shop
     * @throws \Exception
     */
    public function injectHooks(Shop $shop)
    {
        // ---- inject hooks
        $this->registerUninstallHook($shop);
        $this->registerScriptHook($shop);
    }

    /**
     * @param $shop
     * @param $topic
     * @param $route
     * @return array
     */
    public function registerHook($shop, $topic, $route)
    {
        $shop = $shop instanceof Shop ? $shop : $this->shopRepo->findByField('name', $shop)->first();
        $response = [
            'status' => '',  // ----- can be ['failure', 'success', 'abort']
            'message' => '',
            'data' => []
        ];

        // ----- register check if the hook already exists
        $shopify = $this->getShopifyObj($shop);
        $hook = $shopify->call([
            'URL' => '/admin/webhooks/count.json?topic='.$topic,
            'METHOD' => 'GET'
        ]);

        if (isset($hook->count) && $hook->count == 0) {
            try {
                $resp = $shopify->call([
                    'METHOD'    => 'POST',
                    'URL'       => '/admin/webhooks.json',
                    'DATA'      => [
                        'webhook' => [
                            'topic'     => $topic,
                            'address'   => $route,
                            'format'    => 'json'
                        ]
                    ]
                ]);

                $response['status'] = 'success';
                $response['message'] = 'Hook injected successfully.';
                $response['data'] = $resp->webhook;

            } catch (\Exception $e) {
                $response['status'] = 'failure';
                $response['message'] = $e->getMessage();
            }
        } else {
            $response['abort'] = 'failure';
            $response['message'] = 'Hook already exists.';
        }

        return $response;
    }

    /**
     * Hook for loading pixel Engine on each page load of storefront
     * @param $shop
     * @throws \Exception
     */
    public function registerScriptHook($shop)
    {
        $shop = $shop instanceof Shop ? $shop : $this->shopRepo->findByField('domain', $shop)->first();
        $shopify = $this->getShopifyObj($shop);

        // ----- existing hook cleanup
        $hook = $shopify->call([
            'URL' => '/admin/script_tags.json',
            'METHOD' => 'GET'
        ]);
        if (isset($hook->script_tags)) {
            foreach ($hook->script_tags as $crntTag) {
                $shopify->call([
                    'URL' => '/admin/script_tags/'.$crntTag->id.'.json',
                    'METHOD' => 'DELETE'
                ]);
            }
        }
    }

    /**
     * @param $shop
     * @return array
     * @throws \Exception
     */
    public function registerUninstallHook($shop)
    {
        return $this->registerHook($shop, 'app/uninstalled', route('hooks.uninstall'));
    }

}
