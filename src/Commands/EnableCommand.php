<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Domain;
use Symfony\Component\Console\Input\InputArgument;

class EnableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:enable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable the specified domain.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {

        $this->components->info('Enabling domain ...');

        if ($name = $this->argument('domain') ) {
            $this->enable($name);

            return 0;
        }

        $this->enableAll();

        return 0;
    }

    /**
     * enableAll
     *
     * @return void
     */
    public function enableAll()
    {
        /** @var Domains $domains */
        $domains = $this->laravel['domains']->all();

        foreach ($domains as $domain) {
            $this->enable($domain);
        }
    }

    /**
     * enable
     *
     * @param string $name
     * @return void
     */
    public function enable($name)
    {
        if ($name instanceof Domain) {
            $domain = $name;
        }else {
            $domain = $this->laravel['domains']->findOrFail($name);
        }

        if ($domain->isDisabled()) {
            $domain->enable();

            $this->components->info("Domain [{$domain}] enabled successful.");
        }else {
            $this->components->warn("Domain [{$domain}] has already enabled.");
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
            ['domain', InputArgument::OPTIONAL, 'Domain name.'],
        ];
    }
}
