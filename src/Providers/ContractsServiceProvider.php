<?php

namespace Salah3id\Domains\Providers;

use Illuminate\Support\ServiceProvider;
use Salah3id\Domains\Contracts\RepositoryInterface;
use Salah3id\Domains\Laravel\LaravelFileRepository;

class ContractsServiceProvider extends ServiceProvider
{
    /**
     * Register some binding.
     */
    public function register()
    {
        $this->app->bind(RepositoryInterface::class, LaravelFileRepository::class);
    }
}
