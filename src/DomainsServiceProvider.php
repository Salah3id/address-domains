<?php

namespace Salah3id\Domains;

use Illuminate\Support\ServiceProvider;
use Salah3id\Domains\Providers\BootstrapServiceProvider;
use Salah3id\Domains\Providers\ConsoleServiceProvider;
use Salah3id\Domains\Providers\ContractsServiceProvider;

abstract class DomainsServiceProvider extends ServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
    }

    /**
     * Register all domains.
     */
    public function register()
    {
    }

    /**
     * Register all domains.
     */
    protected function registerDomains()
    {
        $this->app->register(BootstrapServiceProvider::class);
    }

    /**
     * Register package's namespaces.
     */
    protected function registerNamespaces()
    {
        $configPath = __DIR__ . '/../config/config.php';
        $stubsPath = dirname(__DIR__) . '/src/Commands/stubs';

        $this->publishes([
            $configPath => config_path('domains.php'),
        ], 'config');

        $this->publishes([
            $stubsPath => base_path('stubs/salah3id-stubs'),
        ], 'stubs');
    }

    /**
     * Register the service provider.
     */
    abstract protected function registerServices();

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Contracts\RepositoryInterface::class, 'domains'];
    }

    /**
     * Register providers.
     */
    protected function registerProviders()
    {
        $this->app->register(ConsoleServiceProvider::class);
        $this->app->register(ContractsServiceProvider::class);
    }
}
