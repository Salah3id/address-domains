<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Migrations\Migrator;
use Salah3id\Domains\Domain;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateStatusCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:migrate-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Status for all domain migrations';

    /**
     * @var \Salah3id\Domains\Contracts\RepositoryInterface
     */
    protected $domain;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(): int
    {
        $this->domain = $this->laravel['domains'];

        $name = $this->argument('domain');

        if ($name) {
            $domain = $this->domain->findOrFail($name);

            $this->migrateStatus($domain);

            return 0;
        }

        foreach ($this->domain->getOrdered($this->option('direction')) as $domain) {
            $this->line('Running for domain: <info>' . $domain->getName() . '</info>');
            $this->migrateStatus($domain);
        }

        return 0;
    }

    /**
     * Run the migration from the specified domain.
     *
     * @param Domain $domain
     */
    protected function migrateStatus(Domain $domain)
    {
        $path = str_replace(base_path(), '', (new Migrator($domain, $this->getLaravel()))->getPath());

        $this->call('migrate:status', [
            '--path' => $path,
            '--database' => $this->option('database'),
        ]);
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
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
        ];
    }
}
