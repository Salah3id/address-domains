<?php

namespace Salah3id\Domains;

use Salah3id\Domains\Support\Stub;

class LumenDomainsServiceProvider extends DomainsServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->setupStubPath();
    }

    /**
     * Register all domains.
     */
    public function register()
    {
        $this->registerNamespaces();
        $this->registerServices();
        $this->registerDomains();
        $this->registerProviders();
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        Stub::setBasePath(__DIR__ . '/Commands/stubs');

        if (app('domains')->config('stubs.enabled') === true) {
            Stub::setBasePath(app('domains')->config('stubs.path'));
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('domains.paths.domains');

            return new Lumen\LumenFileRepository($app, $path);
        });
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('domains.activator');
            $class = $app['config']->get('domains.activators.' . $activator)['class'];

            return new $class($app);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'domains');
    }
}
