<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Domain;
use Symfony\Component\Console\Input\InputArgument;

class DumpCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:dump';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dump-autoload the specified domain or for all domain.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Generating optimized autoload domains.');

        if ($name = $this->argument('domain') ) {
            $this->dump($name);

            return 0;
        }

        $this->dumpAll();

        return 0;
    }

    /**
     * dumpAll
     *
     * @return void
     */
    public function dumpAll()
    {
        /** @var Domains $domains */
        $domains = $this->laravel['domains']->all();

        foreach ($domains as $domain) {
            $this->dump($domain);
        }
    }

    public function dump($name)
    {
        if ($name instanceof Domain) {
            $domain = $name;
        } else {
            $domain = $this->laravel['domains']->findOrFail($name);
        }

        $this->components->task("$domain", function () use ($domain) {
            chdir($domain->getPath());

            passthru('composer dump -o -n -q');
        });

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::OPTIONAL, 'Domain name.'],
        ];
    }
}
