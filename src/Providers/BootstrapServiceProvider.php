<?php

namespace Salah3id\Domains\Providers;

use Illuminate\Support\ServiceProvider;
use Salah3id\Domains\Contracts\RepositoryInterface;

class BootstrapServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot(): void
    {
        $this->app[RepositoryInterface::class]->boot();
    }

    /**
     * Register the provider.
     */
    public function register(): void
    {
        $this->app[RepositoryInterface::class]->register();
        $this->app->register('Salah3id\Domains\Repository\Providers\EventServiceProvider');
    }
}
