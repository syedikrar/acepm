<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;

\URL::forceScheme('https');
\URL::forceRootUrl(env('APP_URL'));

Route::post('sociallogin/{provider}', 'Auth\RegisterController@socialLogin');
//Route::get('auth/{provider}/callback', 'Auth\RegisterController@socialLoginCallBack')->where('provider', '.*');

Route::prefix('/shopify')->group(function(){
    Route::get('/', [
        'as' => 'shopify.install',
        'uses' => 'ShopifyController@install'
    ]);

    // ----- auth callback
    Route::get('/auth-callback', [
        'as' => 'shopify.auth-callback',
        'uses' => 'ShopifyController@authCallback'
    ]);

    // ----- payment confirmation
    Route::get('/charge-callback', [
        'as' => 'shopify.charge-callback',
        'uses' => 'ShopifyController@chargeCallback'
    ]);

});

Route::prefix('/pull')->group(function() {
    // ----- free shipping bar hook
    Route::get('/{shop}/free-shipping-hook.js', [
        'as'    => 'pull-free-shipping',
        'uses'  => 'PullController@freeShipping'
    ]);

    // ----- sticky cart hook
    Route::get('/{shop}/sticky-cart-hook.js', [
        'as'    => 'pull-sticky-cart',
        'uses'  => 'PullController@stickyCart'
    ]);

    // ----- countdown timer hook
    Route::get('/{shop}/count-down-hook.js', [
        'as'    => 'pull-count-down',
        'uses'  => 'PullController@countdownTimer'
    ]);
});

Route::prefix('/hook')->middleware(['odin'])->group(function(){
    // ----- uninstall hook callback
    Route::post('/uninstall', [
        'as' => 'hooks.uninstall',
        'uses'  => 'IntegrityController@uninstall'
    ]);

    // ----- uninstall hook callback
    Route::post('/theme-published', [
        'as' => 'hooks.themePublished',
        'uses'  => 'IntegrityController@themePublished'
    ]);

    // ----- GDPR Customer data erasure callback
    Route::post('/erase-customer', [
        'as' => 'hooks.eraseCustomers',
        'uses'  => 'IntegrityController@eraseCustomer'
    ]);

    // ----- GDPR get Customer data callback
    Route::get('/shop-data', [
        'as' => 'hooks.userData',
        'uses'  => 'IntegrityController@shopData'
    ]);

    // ----- GDPR Shop Data erasure callback
    Route::post('/erase-shop', [
        'as' => 'hooks.eraseShop',
        'uses'  => 'IntegrityController@eraseShop'
    ]);
});

Route::get("approve-account/{user}", "ShopifyController@approve")->name("approve.account");

// ------ Attachments
Route::prefix('attachments')->group(function(){
    Route::get('download/{name}', 'CardAttachmentsController@download');
});

// ----- Social Login related routes
Route::get('/google/callback', 'UserPlatformsController@handleProviderGoogleCallback');


Route::any('{all}', function () {
    return view('app');
})->where(['all' => '.*']);



