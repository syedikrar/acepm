<?php

namespace App\Listeners;

use App\Events\SettingUpdated;
use App\Services\CloudflareService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SettingUpdatedListener
{
    /**
     * @var CloudflareService
     */
    protected $cloudflareService;

    /**
     * Create the event listener.
     *
     * @param CloudflareService $cloudflareService
     */
    public function __construct(CloudflareService $cloudflareService)
    {
        $this->cloudflareService = $cloudflareService;
    }

    /**
     * Handle the event.
     *
     * @param  SettingUpdated  $event
     * @return void
     */
    public function handle(SettingUpdated $event)
    {
        if (env('CLOUDFLARE_CACHE', false) && env('APP_ENV') == 'production') {
            $this->cloudflareService->purgePullCache($event->shop);
        }
    }
}
