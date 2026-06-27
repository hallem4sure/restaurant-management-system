<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Contracts\Services\MenuServiceInterface::class,
            \App\Services\MenuService::class
        );
        $this->app->bind(
            \App\Contracts\Services\TableServiceInterface::class,
            \App\Services\TableService::class
        );
        $this->app->bind(
            \App\Contracts\Services\OfferServiceInterface::class,
            \App\Services\OfferService::class
        );
        $this->app->bind(
            \App\Contracts\Services\ReservationServiceInterface::class,
            \App\Services\ReservationService::class
        );
        $this->app->bind(
            \App\Contracts\Services\OrderServiceInterface::class,
            \App\Services\OrderService::class
        );
        $this->app->bind(
            \App\Contracts\Services\KitchenServiceInterface::class,
            \App\Services\KitchenService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
