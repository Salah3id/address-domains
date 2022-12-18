<?php
namespace Salah3id\Domains\Repository\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class RepositoryServiceProvider
 * @package Salah3id\Domains\Repository\Providers
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;


    /**
     *
     * @return void
     */
    public function boot()
    {
        //
    }


    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\RepositoryCommand');
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\TransformerCommand');
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\PresenterCommand');
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\EntityCommand');
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\ValidatorCommand');
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\ControllerCommand');
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\BindingsCommand');
        $this->commands('Salah3id\Domains\Repository\Generators\Commands\CriteriaCommand');
        $this->app->register('Salah3id\Domains\Repository\Providers\EventServiceProvider');
    }


    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [];
    }
}
