<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['guest:api']], function() {
    Route::post('login', 'Auth\LoginController@login');
    Route::post('login/refresh', 'Auth\LoginController@refresh');

    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    Route::post('password/reset', 'Auth\ResetPasswordController@reset');

    Route::post('register', 'Auth\RegisterController@register');    
});

// ----- shopify handshake
Route::get('/shopify/install/{shop}', [
    'as' => 'shopify.install-path',
    'uses' => 'ShopifyController@installPath'
]);

Route::group(['middleware' => ['jwt']], function() {
    Route::post('logout', 'Auth\LoginController@logout');

    Route::get('users', 'ProfileController@getUsers');
    Route::get('users/teamTasks','ProfileController@teamTasks');
    Route::get('users/best-sellers', 'ProfileController@getBestSellers');
    Route::get('me', 'Auth\LoginController@me');
    Route::post('profile', 'ProfileController@update');
    Route::put('users/{id}/update-status', 'ProfileController@updateStatus');
    Route::delete('users/{id}/delete', 'ProfileController@delete');

    Route::prefix('/shopify')->group(function(){

        // ----- linked shops
        Route::get('linked-shops', [
            'as' => 'shopify-linked-shops',
            'uses' => 'ShopifyController@linkedShops'
        ]);

        // ----- linked shops
        Route::get('user-shops', [
            'as' => 'shopify-user-shops',
            'uses' => 'ShopifyController@userShops'
        ]);

        // ----- restart billing
        Route::get('plan-selected/{plan}', [
            'as' => 'shopify.plan-selected',
            'uses' => 'ShopifyController@planSelected'
        ]);

        // ----- clean uninstall
        Route::patch('clean-uninstall', [
            'as' => 'shopify.clean-uninstall',
            'uses'  => 'ShopifyController@cleanUninstall'
        ]);
    });

    // ------ templates
    Route::prefix('templates')->group(function(){
        Route::get('/all', 'BoardTemplatesController@all');
        Route::get('/field/{field}', 'BoardTemplatesController@byField');
        Route::get('/{id}', 'BoardTemplatesController@get');
        Route::post('save', 'BoardTemplatesController@save');
        Route::delete('delete/{id}', 'BoardTemplatesController@delete');
    });

    // ------ boards
    Route::prefix('boards')->group(function(){
        Route::get('/all', 'BoardsController@all');
        Route::get('/field/{field}', 'BoardsController@byField');
        Route::get('/{id}', 'BoardsController@get');
        Route::post('save', 'BoardsController@save');
        Route::post('/from-template', 'BoardsController@fromTemplate');
        Route::delete('delete/{id}', 'BoardsController@delete');
        Route::post('restore/{id}', 'BoardsController@restore');
        Route::post('background-image', 'BoardsController@uploadBackgroundImage');
        Route::delete('{id}/background-image', 'BoardsController@deleteBackgroundImage');
    });

    // ------ cards
    Route::prefix('cards')->group(function(){
        Route::get('/{id}', 'CardsController@get');
        Route::post('save', 'CardsController@save');
        Route::delete('delete/{id}', 'CardsController@delete');
    });

    // ------ Attachments
    Route::prefix('attachments')->group(function(){
        Route::post('save', 'CardAttachmentsController@save');
        Route::delete('delete/{id}', 'CardAttachmentsController@delete');
    });

    // ------ Time trackers
    Route::prefix('time-trackers')->group(function(){
        Route::post('save', 'TimeTrackersController@save');
    });

    // ------ Sub Tasks
    Route::prefix('sub-tasks')->group(function(){
        Route::get('/{id}', 'SubTasksController@get');
        Route::post('save', 'SubTasksController@save');
        Route::delete('delete/{id}', 'SubTasksController@delete');
    });

    // ------ Comments
    Route::prefix('comments')->group(function(){
        Route::get('/{id}', 'CommentsController@get');
        Route::post('save', 'CommentsController@save');
        Route::delete('delete/{id}', 'CommentsController@delete');
    });

    // ----- integrity routes
    Route::prefix('/integrity')->group(function(){
        Route::get('check', 'IntegrityController@check');
        Route::patch('fix', 'IntegrityController@fix');
        Route::get('shop-info.json', 'IntegrityController@shopInfo');
        Route::get('field-types.json', 'IntegrityController@fieldTypes');
    });

    // ----- discount coupons routes
    Route::prefix('/discount-coupons')->group(function(){
        Route::get('get.json', 'DiscountCouponsController@get');
        Route::patch('save', 'DiscountCouponsController@save');
        Route::post('check/{code}', [
            'as' => 'coupon.check',
            'uses' => 'DiscountCouponsController@check'
        ]);
        Route::delete('delete/{id}', 'DiscountCouponsController@delete');
    });

    // ----- campaigns routes
    Route::prefix('/upsell-campaign')->group(function(){
        Route::get('active.json', 'UpsellCampaignsController@active');
        Route::get('get.json', 'UpsellCampaignsController@get');
        Route::patch('save', 'UpsellCampaignsController@save');
        Route::post('stats', 'UpsellCampaignsController@recordStats');
        Route::delete('delete/{id}', 'UpsellCampaignsController@delete');
        Route::patch('last-seen', 'ShopMetasController@updateLastSeen');
        Route::patch('toggle', 'UpsellCampaignsController@toggle');
    });

    // ------ Gigs routes
    Route::prefix('gigs')->group(function(){
        Route::get("all","GigsController@all");
        Route::get("categories","GigsController@categories");
        Route::get("sub_categories/{id}","GigsController@sub_categories");
        Route::post('save', 'GigsController@save');
        Route::get("show/{id}","GigsController@show");
        Route::post("order", "GigsController@order");
        Route::get("contracts","GigsController@contracts");
        Route::get("contracts-seller","GigsController@sellerContracts");
        Route::delete("contracts/{contract}/delete","GigsController@deleteContract");
        Route::post("/job/save", "GigsController@saveJob");
        Route::get("/jobs", "GigsController@getJobs");
    });

    Route::prefix('/resource')->group(function(){
        Route::get('/get-products','ResourceController@getProducts');
        Route::get('/get-orders','ResourceController@getOrders');

    });

    Route::prefix('/shopify-integration')->group(function(){
        Route::post('/save','ShopifyIntegrationsController@save');
        Route::delete('delete/{id}', 'ShopifyIntegrationsController@delete');

    });

    Route::prefix('/google-drive-integration')->group(function(){
        Route::post('/socialPlatformLogin', 'GoogleDriveIntegrationsController@redirectToGoogleProvider');
        Route::get('/getSavedDataByCardId/{id}', 'GoogleDriveIntegrationsController@getSavedDataByCardId');
        Route::post('/save','GoogleDriveIntegrationsController@save');
        Route::delete('delete/{id}', 'GoogleDriveIntegrationsController@delete');
    });

    Route::prefix('/dropbox-integration')->group(function(){
        Route::get('/getSavedDataByCardId/{id}', 'DropboxIntegrationsController@getSavedDataByCardId');
        Route::post('/save','DropboxIntegrationsController@save');
        Route::delete('delete/{id}', 'DropboxIntegrationsController@delete');
    });

    Route::prefix('/social-login')->group(function(){
        Route::post('/socialPlatformLogin', 'UserPlatformsController@redirectToGoogleProvider');
    });

});
