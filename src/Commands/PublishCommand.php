<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Domain;
use Salah3id\Domains\Publishing\AssetPublisher;
use Symfony\Component\Console\Input\InputArgument;

class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a domain\'s assets to the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('Publishing domain assets...');

        if ($name = $this->argument('domain')) {
            $this->publish($name);

            return 0;
        }

        $this->publishAll();

        return 0;
    }

    /**
     * Publish assets from all domains.
     */
    public function publishAll()
    {
        foreach ($this->laravel['domains']->allEnabled() as $domain) {
            $this->publish($domain);
        }
    }

    /**
     * Publish assets from the specified domain.
     *
     * @param string $name
     */
    public function publish($name)
    {
        if ($name instanceof Domain) {
            $domain = $name;
        } else {
            $domain = $this->laravel['domains']->findOrFail($name);
        }

        with(new AssetPublisher($domain))
            ->setRepository($this->laravel['domains'])
            ->setConsole($this)
            ->publish();

        $this->components->task($domain->getStudlyName(), fn()=>true);
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
}
