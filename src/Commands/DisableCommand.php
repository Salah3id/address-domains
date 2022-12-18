<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Domain;
use Symfony\Component\Console\Input\InputArgument;

class DisableCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:disable';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Disable the specified domain.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Disabling domain ...');

        if ($name = $this->argument('domain') ) {
            $this->disable($name);

            return 0;
        }

        $this->disableAll();

        return 0;
    }

    /**
     * disableAll
     *
     * @return void
     */
    public function disableAll()
    {
        /** @var Domains $domains */
        $domains = $this->laravel['domains']->all();

        foreach ($domains as $domain) {
            $this->disable($domain);
        }
    }

    /**
     * disable
     *
     * @param string $name
     * @return void
     */
    public function disable($name)
    {
        if ($name instanceof Domain) {
            $domain = $name;
        }else {
            $domain = $this->laravel['domains']->findOrFail($name);
        }

        if ($domain->isEnabled()) {
            $domain->disable();

            $this->components->info("Domain [{$domain}] disabled successful.");
        } else {
            $this->components->warn("Domain [{$domain}] has already disabled.");
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
