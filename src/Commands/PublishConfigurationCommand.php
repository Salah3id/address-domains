<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class PublishConfigurationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:publish-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish a domain\'s config files to the application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->info('publishing domain config files...');

        if ($domain = $this->argument('domain')) {
            $this->publishConfiguration($domain);

            return 0;
        }

        foreach ($this->laravel['domains']->allEnabled() as $domain) {
            $this->publishConfiguration($domain->getName());
        }

        return 0;
    }

    /**
     * @param string $domain
     * @return string
     */
    private function getServiceProviderForDomain($domain)
    {
        $namespace = $this->laravel['config']->get('domains.namespace');
        $studlyName = Str::studly($domain);

        return "$namespace\\$studlyName\\Providers\\{$studlyName}ServiceProvider";
    }

    /**
     * @param string $domain
     */
    private function publishConfiguration($domain)
    {
        $this->call('vendor:publish', [
            '--provider' => $this->getServiceProviderForDomain($domain),
            '--force' => $this->option('force'),
            '--tag' => ['config'],
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
            ['domain', InputArgument::OPTIONAL, 'The name of domain being used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['--force', '-f', InputOption::VALUE_NONE, 'Force the publishing of config files'],
        ];
    }
}
