<?php

namespace App\Providers;

use App\Repositories\Contracts\CltLayerRepositoryInterface;
use App\Repositories\Contracts\CltLayupRepositoryInterface;
use App\Repositories\Contracts\SupplierRepositoryInterface;
use App\Repositories\Eloquent\CltLayerRepository;
use App\Repositories\Eloquent\CltLayupRepository;
use App\Repositories\Eloquent\SupplierRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(SupplierRepositoryInterface::class, SupplierRepository::class);
        $this->app->bind(CltLayupRepositoryInterface::class, CltLayupRepository::class);
        $this->app->bind(CltLayerRepositoryInterface::class, CltLayerRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
