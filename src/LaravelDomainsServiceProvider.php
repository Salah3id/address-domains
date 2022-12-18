<?php

namespace Salah3id\Domains;

use Salah3id\Domains\Contracts\RepositoryInterface;
use Salah3id\Domains\Exceptions\InvalidActivatorClass;
use Salah3id\Domains\Support\Stub;

class LaravelDomainsServiceProvider extends DomainsServiceProvider
{
    /**
     * Booting the package.
     */
    public function boot()
    {
        $this->registerNamespaces();
        $this->registerDomains();
    }

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerServices();
        $this->setupStubPath();
        $this->registerProviders();

        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'domains');
    }

    /**
     * Setup stub path.
     */
    public function setupStubPath()
    {
        $path = $this->app['config']->get('domains.stubs.path') ?? __DIR__ . '/Commands/stubs';
        Stub::setBasePath($path);

        $this->app->booted(function ($app) {
            /** @var RepositoryInterface $domainRepository */
            $domainRepository = $app[RepositoryInterface::class];
            if ($domainRepository->config('stubs.enabled') === true) {
                Stub::setBasePath($domainRepository->config('stubs.path'));
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    protected function registerServices()
    {
        $this->app->singleton(Contracts\RepositoryInterface::class, function ($app) {
            $path = $app['config']->get('domains.paths.domains');

            return new Laravel\LaravelFileRepository($app, $path);
        });
        $this->app->singleton(Contracts\ActivatorInterface::class, function ($app) {
            $activator = $app['config']->get('domains.activator');
            $class = $app['config']->get('domains.activators.' . $activator)['class'];

            if ($class === null) {
                throw InvalidActivatorClass::missingConfig();
            }

            return new $class($app);
        });
        $this->app->alias(Contracts\RepositoryInterface::class, 'domains');
    }
}
