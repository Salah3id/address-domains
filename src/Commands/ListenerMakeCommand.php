<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Domain;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ListenerMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;

    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:make-listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new event listener class for the specified domain';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the command.'],
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
            ['event', 'e', InputOption::VALUE_OPTIONAL, 'The event class being listened for.'],
            ['queued', null, InputOption::VALUE_NONE, 'Indicates the event listener should be queued.'],
        ];
    }

    protected function getTemplateContents()
    {
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($domain),
            'EVENTNAME' => $this->getEventName($domain),
            'SHORTEVENTNAME' => $this->getShortEventName(),
            'CLASS' => $this->getClass(),
        ]))->render();
    }

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.listener.namespace') ?: $domain->config('paths.generator.listener.path', 'Listeners');
    }

    protected function getEventName(Domain $domain)
    {
        $namespace = $this->laravel['domains']->config('namespace') . "\\" . $domain->getStudlyName();
        $eventPath = GenerateConfigReader::read('event');

        $eventName = $namespace . "\\" . $eventPath->getPath() . "\\" . $this->option('event');

        return str_replace('/', '\\', $eventName);
    }

    protected function getShortEventName()
    {
        return class_basename($this->option('event'));
    }

    protected function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $listenerPath = GenerateConfigReader::read('listener');

        return $path . $listenerPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    protected function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * @return string
     */
    protected function getStubName(): string
    {
        if ($this->option('queued')) {
            if ($this->option('event')) {
                return '/listener-queued.stub';
            }

            return '/listener-queued-duck.stub';
        }

        if ($this->option('event')) {
            return '/listener.stub';
        }

        return '/listener-duck.stub';
    }
}
