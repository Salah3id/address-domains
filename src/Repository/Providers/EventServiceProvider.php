<?php
namespace Salah3id\Domains\Repository\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class EventServiceProvider
 * @package Salah3id\Domains\Repository\Providers
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class EventServiceProvider extends ServiceProvider
{

    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Salah3id\Domains\Repository\Events\RepositoryEntityCreated' => [
            'Salah3id\Domains\Repository\Listeners\CleanCacheRepository'
        ],
        'Salah3id\Domains\Repository\Events\RepositoryEntityUpdated' => [
            'Salah3id\Domains\Repository\Listeners\CleanCacheRepository'
        ],
        'Salah3id\Domains\Repository\Events\RepositoryEntityDeleted' => [
            'Salah3id\Domains\Repository\Listeners\CleanCacheRepository'
        ]
    ];

    /**
     * Register the application's event listeners.
     *
     * @return void
     */
    public function boot()
    {
        $events = app('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        //
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}
