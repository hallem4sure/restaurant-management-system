<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\Services\MenuServiceInterface;
use App\Services\MenuService;
use App\Contracts\Services\TableServiceInterface;
use App\Services\TableService;
use App\Contracts\Services\OfferServiceInterface;
use App\Services\OfferService;
use App\Contracts\Services\ReservationServiceInterface;
use App\Services\ReservationService;
use App\Contracts\Services\OrderServiceInterface;
use App\Services\OrderService;
use App\Contracts\Services\KitchenServiceInterface;
use App\Services\KitchenService;
use App\Contracts\Services\BillServiceInterface;
use App\Services\BillService;
use App\Contracts\Services\UserServiceInterface;
use App\Services\UserService;
use App\Contracts\Services\SettingServiceInterface;
use App\Services\SettingService;

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
        $this->app->bind(
            \App\Contracts\Services\BillServiceInterface::class,
            \App\Services\BillService::class
        );
        $this->app->bind(
            \App\Contracts\Services\UserServiceInterface::class,
            \App\Services\UserService::class
        );
        $this->app->bind(
            \App\Contracts\Services\SettingServiceInterface::class,
            \App\Services\SettingService::class
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
