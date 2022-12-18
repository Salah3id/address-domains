<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

class DomainDeleteCommand extends Command
{
    protected $name = 'domain:delete';
    protected $description = 'Delete a domain from the application';

    public function handle(): int
    {
        $this->laravel['domains']->delete($this->argument('domain'));

        $this->components->info("Domain {$this->argument('domain')} has been deleted.");

        return 0;
    }

    protected function getArguments()
    {
        return [
            ['domain', InputArgument::REQUIRED, 'The name of domain to delete.'],
        ];
    }
}
