<?php

namespace App\Services;

use App\Models\Shop;
use GuzzleHttp\Client;

/**
 * Class CoudflareService
 *
 * @package \App\Services
 */
class CloudflareService
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct()
    {
        $this->prepClient();
    }

    public function purgePullCache(Shop $shop)
    {
        $zones = $this->getZones();

        try {
            $this->removeCache([
                route('pull-free-shipping', ['shop' => $shop->domain]),
                route('pull-free-shipping', ['shop' => $shop->domain]).'?shop='.$shop->domain,

                route('pull-sticky-cart', ['shop' => $shop->domain]),
                route('pull-sticky-cart', ['shop' => $shop->domain]).'?shop='.$shop->domain,

                route('pull-count-down', ['shop' => $shop->domain]),
                route('pull-count-down', ['shop' => $shop->domain]).'?shop='.$shop->domain,
            ], $zones);
        } catch (\Exception $e) {
            \Log::info('Cloudflare Cache Purge Error');
            \Log::info($e->getMessage());
            return ['status' => true];
        }
    }

    public function getZones($page = 1, $per_page = 100)
    {
        $resp = $this->client->get('client/v4/zones', [
            'query' => [
                'status' => 'active',
                'page'   => $page,
                'per_page' => $per_page,
                'order' => 'status',
                'direction' => 'desc',
                'match' => 'all'
            ]
        ]);

        $zones = json_decode($resp->getBody());
        $result = $zones->result ?? null;
        $ok = $zones->success ?? false;
        if (!$ok) {
            throw new Exception("not success" . ($zones->errors ?? '') . ($zones->messages ?? ''));
        }
        if (!$result) {
            throw new Exception("result null");
        }
        return $zones->result;
    }

    /**
     * Method to purge specific URLs from the cache
     *
     * @param $urls
     * @param $zoneData  zone data
     *
     * @return array
     * @throws \Exception
     */
    public function removeCache($urls, $zoneData)
    {
        $zones = [];
        foreach ($urls as $url) {
            $baseURL = parse_url($url)['host'];
            $domain = $this->get_domain($baseURL);
            $id = '';
            // the zone id should be cached
            if (array_key_exists($domain, $zones)) {
                $id = $zones[$domain];
            } else {
                foreach ($zoneData as $tmp) {
                    if ($tmp->name == $domain) {
                        $id = $tmp->id;
                        $zones[$domain] = $id;
                    }
                }
            }
            if ($id == '') throw new \Exception("$domain not found");

            $resp = $this->client->delete('client/v4/zones/'.$id.'/purge_cache', [
                'body' => json_encode(
                    [
                        'files' => [$url]
                    ]
                )
            ]);
            $rr = json_decode($resp->getBody());

            if (!isset($rr->success)) {
                throw new Exception($rr->errors . '  ' . $rr->messages);
            }
        }
        return [
            'status' => true
        ];
    }

    private function get_domain($host){
        $myhost = strtolower(trim($host));
        $count = substr_count($myhost, '.');
        if($count === 2){
            if(strlen(explode('.', $myhost)[1]) > 3) $myhost = explode('.', $myhost, 2)[1];
        } else if($count > 2){
            $myhost = get_domain(explode('.', $myhost, 2)[1]);
        }
        return $myhost;
    }

    private function prepClient()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.cloudflare.com',
            'headers' => [
                'Content-Type'      => 'application/json',
                'Accept'            => 'application/json',
                'X-Auth-Email'      => env('CLOUDFLARE_EMAIL'),
                'X-Auth-Key'        =>  env('CLOUDFLARE_KEY')
            ]
        ]);
    }

}
