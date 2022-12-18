<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ControllerMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;

    /**
     * The name of argument being used.
     *
     * @var string
     */
    protected $argumentName = 'controller';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:make-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new restful controller for the specified domain.';

    /**
     * Get controller name.
     *
     * @return string
     */
    public function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $controllerPath = GenerateConfigReader::read('controller');

        return $path . $controllerPath->getPath() . '/' . $this->getControllerName() . '.php';
    }

    /**
     * @return string
     */
    protected function getTemplateContents()
    {
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub($this->getStubName(), [
            'DOMAINNAME'        => $domain->getStudlyName(),
            'CONTROLLERNAME'    => $this->getControllerName(),
            'NAMESPACE'         => $domain->getStudlyName(),
            'CLASS_NAMESPACE'   => $this->getClassNamespace($domain),
            'CLASS'             => $this->getControllerNameWithoutNamespace(),
            'LOWER_NAME'        => $domain->getLowerName(),
            'DOMAIN'            => $this->getDomainName(),
            'NAME'              => $this->getDomainName(),
            'STUDLY_NAME'       => $domain->getStudlyName(),
            'DOMAIN_NAMESPACE'  => $this->laravel['domains']->config('namespace'),
        ]))->render();
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['controller', InputArgument::REQUIRED, 'The name of the controller class.'],
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be used.'],
        ];
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['plain', 'p', InputOption::VALUE_NONE, 'Generate a plain controller', null],
            ['api', null, InputOption::VALUE_NONE, 'Exclude the create and edit methods from the controller.'],
        ];
    }

    /**
     * @return array|string
     */
    protected function getControllerName()
    {
        $controller = Str::studly($this->argument('controller'));

        if (Str::contains(strtolower($controller), 'controller') === false) {
            $controller .= 'Controller';
        }

        return $controller;
    }

    /**
     * @return array|string
     */
    private function getControllerNameWithoutNamespace()
    {
        return class_basename($this->getControllerName());
    }

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.controller.namespace') ?: $domain->config('paths.generator.controller.path', 'Http/Controllers');
    }

    /**
     * Get the stub file name based on the options
     * @return string
     */
    protected function getStubName()
    {
        if ($this->option('plain') === true) {
            $stub = '/controller-plain.stub';
        } elseif ($this->option('api') === true) {
            $stub = '/controller-api.stub';
        } else {
            $stub = '/controller.stub';
        }

        return $stub;
    }
}
