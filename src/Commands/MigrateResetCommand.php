<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Migrations\Migrator;
use Salah3id\Domains\Traits\MigrationLoaderTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateResetCommand extends Command
{
    use MigrationLoaderTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:migrate-reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset the domains migrations.';

    /**
     * @var \Salah3id\Domains\Contracts\RepositoryInterface
     */
    protected $domain;

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->domain = $this->laravel['domains'];

        $name = $this->argument('domain');

        if (!empty($name)) {
            $this->reset($name);

            return 0;
        }

        foreach ($this->domain->getOrdered($this->option('direction')) as $domain) {
            $this->line('Running for domain: <info>' . $domain->getName() . '</info>');

            $this->reset($domain);
        }

        return 0;
    }

    /**
     * Rollback migration from the specified domain.
     *
     * @param $domain
     */
    public function reset($domain)
    {
        if (is_string($domain)) {
            $domain = $this->domain->findOrFail($domain);
        }

        $migrator = new Migrator($domain, $this->getLaravel());

        $database = $this->option('database');

        if (!empty($database)) {
            $migrator->setDatabase($database);
        }

        $migrated = $migrator->reset();

        if (count($migrated)) {
            foreach ($migrated as $migration) {
                $this->line("Rollback: <info>{$migration}</info>");
            }

            return;
        }

        $this->comment('Nothing to rollback.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be used.'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'desc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
        ];
    }
}
