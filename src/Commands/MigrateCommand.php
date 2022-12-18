<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Migrations\Migrator;
use Salah3id\Domains\Domain;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class MigrateCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate the migrations from the specified domain or from all domains.';

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

            $this->migrate($domain);

            return 0;
        }

        foreach ($this->domain->getOrdered($this->option('direction')) as $domain) {
            $this->line('Running for domain: <info>' . $domain->getName() . '</info>');

            $this->migrate($domain);
        }

        return 0;
    }

    /**
     * Run the migration from the specified domain.
     *
     * @param Domain $domain
     */
    protected function migrate(Domain $domain)
    {
        $path = str_replace(base_path(), '', (new Migrator($domain, $this->getLaravel()))->getPath());

        if ($this->option('subpath')) {
            $path = $path . "/" . $this->option("subpath");
        }

        $this->call('migrate', [
            '--path' => $path,
            '--database' => $this->option('database'),
            '--pretend' => $this->option('pretend'),
            '--force' => $this->option('force'),
        ]);

        if ($this->option('seed')) {
            $this->call('domain:seed', ['domain' => $domain->getName(), '--force' => $this->option('force')]);
        }
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
            ['pretend', null, InputOption::VALUE_NONE, 'Dump the SQL queries that would be run.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
            ['seed', null, InputOption::VALUE_NONE, 'Indicates if the seed task should be re-run.'],
            ['subpath', null, InputOption::VALUE_OPTIONAL, 'Indicate a subpath to run your migrations from'],
        ];
    }
}
