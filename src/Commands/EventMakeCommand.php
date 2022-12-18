<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class EventMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;

    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:make-event';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event class for the specified domain';

    public function getTemplateContents()
    {
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub('/event.stub', [
            'NAMESPACE' => $this->getClassNamespace($domain),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    public function getDestinationFilePath()
    {
        $path       = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $eventPath = GenerateConfigReader::read('event');

        return $path . $eventPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    protected function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.event.namespace') ?: $domain->config('paths.generator.event.path', 'Events');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the event.'],
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be used.'],
        ];
    }
}
