<?php

namespace Salah3id\Domains\Providers;

use Illuminate\Support\ServiceProvider;
use Salah3id\Domains\Commands;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * The available commands
     * @var array
     */
    protected $commands = [
        Commands\CommandMakeCommand::class,
        Commands\ControllerMakeCommand::class,
        Commands\DisableCommand::class,
        Commands\DumpCommand::class,
        Commands\EnableCommand::class,
        Commands\EventMakeCommand::class,
        Commands\JobMakeCommand::class,
        Commands\ListenerMakeCommand::class,
        Commands\MailMakeCommand::class,
        Commands\MiddlewareMakeCommand::class,
        Commands\NotificationMakeCommand::class,
        Commands\ProviderMakeCommand::class,
        Commands\RouteProviderMakeCommand::class,
        Commands\InstallCommand::class,
        Commands\ListCommand::class,
        Commands\DomainDeleteCommand::class,
        Commands\DomainMakeCommand::class,
        Commands\FactoryMakeCommand::class,
        Commands\PolicyMakeCommand::class,
        Commands\RequestMakeCommand::class,
        Commands\RuleMakeCommand::class,
        Commands\MigrateCommand::class,
        Commands\MigrateRefreshCommand::class,
        Commands\MigrateResetCommand::class,
        Commands\MigrateFreshCommand::class,
        Commands\MigrateRollbackCommand::class,
        Commands\MigrateStatusCommand::class,
        Commands\MigrationMakeCommand::class,
        Commands\ModelMakeCommand::class,
        Commands\ModelShowCommand::class,
        Commands\PublishCommand::class,
        Commands\PublishConfigurationCommand::class,
        Commands\PublishMigrationCommand::class,
        Commands\PublishTranslationCommand::class,
        Commands\SeedCommand::class,
        Commands\SeedMakeCommand::class,
        Commands\SetupCommand::class,
        Commands\UnUseCommand::class,
        Commands\UpdateCommand::class,
        Commands\UseCommand::class,
        Commands\ResourceMakeCommand::class,
        Commands\TestMakeCommand::class,
        Commands\LaravelDomainsV6Migrator::class,
        Commands\ComponentClassMakeCommand::class,
        Commands\ComponentViewMakeCommand::class,

        Commands\RepositoryCommand::class,
        Commands\TransformerCommand::class,
        Commands\PresenterCommand::class,
        Commands\EntityCommand::class,
        Commands\ValidatorCommand::class,
        Commands\ControllerCommand::class,
        Commands\BindingsCommand::class,
        Commands\CriteriaCommand::class
    ];

    public function register(): void
    {
        $this->commands(config('domains.commands', $this->commands));
    }

    public function provides(): array
    {
        return $this->commands;
    }
}
