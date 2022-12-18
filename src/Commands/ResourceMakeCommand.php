<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResourceMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;

    protected $argumentName = 'name';
    protected $name = 'domain:make-resource';
    protected $description = 'Create a new resource class for the specified domain.';

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.resource.namespace') ?: $domain->config('paths.generator.resource.path', 'Transformers');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the resource class.'],
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be used.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['collection', 'c', InputOption::VALUE_NONE, 'Create a resource collection.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($domain),
            'CLASS'     => $this->getClass(),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $resourcePath = GenerateConfigReader::read('resource');

        return $path . $resourcePath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * Determine if the command is generating a resource collection.
     *
     * @return bool
     */
    protected function collection(): bool
    {
        return $this->option('collection') ||
            Str::endsWith($this->argument('name'), 'Collection');
    }

    /**
     * @return string
     */
    protected function getStubName(): string
    {
        if ($this->collection()) {
            return '/resource-collection.stub';
        }

        return '/resource.stub';
    }
}
