<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class ComponentClassMakeCommand extends GeneratorCommand
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
    protected $name = 'domain:make-component';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new component-class for the specified domain.';

    public function handle(): int
    {
        if (parent::handle() === E_ERROR) {
            return E_ERROR;
        }
        $this->writeComponentViewTemplate();

        return 0;
    }
    /**
     * Write the view template for the component.
     *
     * @return void
     */
    protected function writeComponentViewTemplate()
    {
        $this->call('domain:make-component-view', ['name' => $this->argument('name') , 'domain' => $this->argument('domain')]);
    }

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.component-class.namespace') ?: $domain->config('paths.generator.component-class.path', 'View/Component');
    }

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
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub('/component-class.stub', [
            'NAMESPACE'         => $this->getClassNamespace($domain),
            'CLASS'             => $this->getClass(),
            'LOWER_NAME'        => $domain->getLowerName(),
            'COMPONENT_NAME'    => 'components.' . Str::lower($this->argument('name')),
        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());
        $factoryPath = GenerateConfigReader::read('component-class');

        return $path . $factoryPath->getPath() . '/' . $this->getFileName();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name')) . '.php';
    }
}
