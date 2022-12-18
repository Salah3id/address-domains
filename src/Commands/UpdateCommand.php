<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Domain;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class UpdateCommand extends Command
{
    use DomainCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update dependencies for the specified domain or for all domains.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Updating domain ...');

        if ($name = $this->argument('domain')) {
            $this->updateDomain($name);

            return 0;
        }

        $this->updateAllDomain();

        return 0;
    }


    protected function updateAllDomain()
    {
        /** @var \Salah3id\Domains\Domain $domain */
        $domains = $this->laravel['domains']->getOrdered();

        foreach ($domains as $domain) {
            $this->updateDomain($domain);
        }

    }

    protected function updateDomain($name)
    {

        if ($name instanceof Domain) {
            $domain = $name;
        }else {
            $domain = $this->laravel['domains']->findOrFail($name);
        }

        $this->components->task("Updating {$domain->getName()} domain", function () use ($domain) {
            $this->laravel['domains']->update($domain);
        });
        $this->laravel['domains']->update($name);

    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be updated.'],
        ];
    }
}
