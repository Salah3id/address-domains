<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class ComponentViewMakeCommand extends GeneratorCommand
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
    protected $name = 'domain:make-component-view';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new component-view for the specified domain.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the component.'],
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be used.'],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        return (new Stub('/component-view.stub', ['QUOTE'=> Inspiring::quote()]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());
        $factoryPath = GenerateConfigReader::read('component-view');

        return $path . $factoryPath->getPath() . '/' . $this->getFileName();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::lower($this->argument('name')) . '.blade.php';
    }
}
