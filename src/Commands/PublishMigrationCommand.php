<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Migrations\Migrator;
use Salah3id\Domains\Publishing\MigrationPublisher;
use Symfony\Component\Console\Input\InputArgument;

class PublishMigrationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:publish-migration';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Publish a domain's migrations to the application";

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('publishing domain migrations...');

        if ($name = $this->argument('domain')) {
            $domain = $this->laravel['domains']->findOrFail($name);

            $this->publish($domain);

            return 0;
        }

        foreach ($this->laravel['domains']->allEnabled() as $domain) {
            $this->publish($domain);
        }

        return 0;
    }

    /**
     * Publish migration for the specified domain.
     *
     * @param \Salah3id\Domains\Domain $domain
     */
    public function publish($domain)
    {
        with(new MigrationPublisher(new Migrator($domain, $this->getLaravel())))
            ->setRepository($this->laravel['domains'])
            ->setConsole($this)
            ->publish();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::OPTIONAL, 'The name of domain being used.'],
        ];
    }
}
