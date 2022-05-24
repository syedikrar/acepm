<?php

namespace App\Providers;

use Google_Client;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class GoogleServiceProvider extends ServiceProvider
{
    public function boot()
    {

    }

    public function register()
    {
        $client = new \Google_Client();
//            Storage::disk('local')->put('client_secret.json', json_encode([
//                'web' => config('services.google')
//            ]));
        $client->setAuthConfig(public_path(env('GOOGLE_CLIENT_CREDENTIALS_PATH')));
        // dd($client);
        return $client;

        $this->app->singleton(Google_Client::class, function ($app) {
            $client = new Google_Client();
            Storage::disk('local')->put('client_secret.json', json_encode([
                'web' => config('services.google')
            ]));
            $client->setAuthConfig(Storage::path('client_secret.json'));
            return $client;
        });
    }
}