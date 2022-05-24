<?php

namespace App\Listeners;

use App\Events\AppUninstalled;
use App\Repositories\HitRepository;
use App\Services\ActiveCamService;
use App\Services\ProductFeedService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AppUninstalledListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {

    }

    /**
     * @param AppUninstalled $event
     */
    public function handle(AppUninstalled $event)
    {
        // ----- delete shop to trigger cascading
        try{
            $event->shop->delete();
        }catch(\Exception $e){

        }
    }
}
