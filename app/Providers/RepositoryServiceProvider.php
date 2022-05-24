<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->bind(\App\Contracts\ShopRepository::class, \App\Repositories\ShopRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ShopUserRepository::class, \App\Repositories\ShopUserRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\BillingRepository::class, \App\Repositories\BillingRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\FreePassRepository::class, \App\Repositories\FreePassRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\DiscountCouponRepository::class, \App\Repositories\DiscountCouponRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\UpsellCampaignRepository::class, \App\Repositories\UpsellCampaignRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ReviewsRepository::class, \App\Repositories\ReviewsRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\BoardTemplateRepository::class, \App\Repositories\BoardTemplateRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\FieldTypeRepository::class, \App\Repositories\FieldTypeRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\BoardRepository::class, \App\Repositories\BoardRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\CardRepository::class, \App\Repositories\CardRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\TimeTrackerRepository::class, \App\Repositories\TimeTrackerRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\SubTaskRepository::class, \App\Repositories\SubTaskRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\ShopifyIntegrationRepository::class, \App\Repositories\ShopifyIntegrationRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\IntegrationShopifyControllerRepository::class, \App\Repositories\IntegrationShopifyControllerRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\CardAttachmentRepository::class, \App\Repositories\CardAttachmentRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\CommentRepository::class, \App\Repositories\CommentRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\GoogleDriveIntegrationRepository::class, \App\Repositories\GoogleDriveIntegrationRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\UserPlatformRepository::class, \App\Repositories\UserPlatformRepositoryEloquent::class);
        $this->app->bind(\App\Contracts\DropboxIntegrationRepository::class, \App\Repositories\DropboxIntegrationRepositoryEloquent::class);
        //:end-bindings:
    }
}
