<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Domain;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ProviderMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;

    /**
     * The name of argument name.
     *
     * @var string
     */
    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:make-provider';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new service provider class for the specified domain.';

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.provider.namespace') ?: $domain->config('paths.generator.provider.path', 'Providers');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The service provider name.'],
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
            ['master', null, InputOption::VALUE_NONE, 'Indicates the master service provider', null],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $stub = $this->option('master') ? 'scaffold/provider' : 'provider';

        /** @var Domain $domain */
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub('/' . $stub . '.stub', [
            'NAMESPACE'         => $this->getClassNamespace($domain),
            'CLASS'             => $this->getClass(),
            'LOWER_NAME'        => $domain->getLowerName(),
            'DOMAIN'            => $this->getDomainName(),
            'NAME'              => $this->getFileName(),
            'STUDLY_NAME'       => $domain->getStudlyName(),
            'DOMAIN_NAMESPACE'  => $this->laravel['domains']->config('namespace'),
            'PATH_VIEWS'        => GenerateConfigReader::read('views')->getPath(),
            'PATH_LANG'         => GenerateConfigReader::read('lang')->getPath(),
            'PATH_CONFIG'       => GenerateConfigReader::read('config')->getPath(),
            'MIGRATIONS_PATH'   => GenerateConfigReader::read('migration')->getPath(),
            'FACTORIES_PATH'    => GenerateConfigReader::read('factory')->getPath(),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $generatorPath = GenerateConfigReader::read('provider');

        return $path . $generatorPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }
}
