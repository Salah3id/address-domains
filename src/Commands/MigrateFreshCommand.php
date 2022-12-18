<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateFreshCommand extends Command
{
    use DomainCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:migrate-fresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Drop all database tables and re-run all migrations';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domain = $this->argument('domain');

        if ($domain && !$this->getDomainName()) {
            $this->error("Domain [$domain] does not exists.");

            return E_ERROR;
        }

        $this->call('migrate:fresh');

        $this->call('domain:migrate', [
            'domain' => $this->getDomainName(),
            '--database' => $this->option('database'),
            '--force' => $this->option('force'),
            '--seed' => $this->option('seed'),
        ]);

        return 0;
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
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to use.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
        ];
    }

    public function getDomainName()
    {
        $domain = $this->argument('domain');

        if (!$domain) {
            return null;
        }

        $domain = app('domains')->find($domain);

        return $domain ? $domain->getStudlyName() : null;
    }
}
