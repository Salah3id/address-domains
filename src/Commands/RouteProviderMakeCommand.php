<?php

namespace Salah3id\Domains\Commands;

use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class RouteProviderMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;

    protected $argumentName = 'domain';

    /**
     * The command name.
     *
     * @var string
     */
    protected $name = 'domain:route-provider';

    /**
     * The command description.
     *
     * @var string
     */
    protected $description = 'Create a new route service provider for the specified domain.';

    /**
     * The command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be used.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the file already exists.'],
        ];
    }

    /**
     * Get template contents.
     *
     * @return string
     */
    protected function getTemplateContents()
    {
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub('/route-provider.stub', [
            'NAMESPACE'            => $this->getClassNamespace($domain),
            'CLASS'                => $this->getFileName(),
            'DOMAIN_NAMESPACE'     => $this->laravel['domains']->config('namespace'),
            'DOMAIN'               => $this->getDomainName(),
            'CONTROLLER_NAMESPACE' => $this->getControllerNameSpace(),
            'WEB_ROUTES_PATH'      => $this->getWebRoutesPath(),
            'API_ROUTES_PATH'      => $this->getApiRoutesPath(),
            'LOWER_NAME'           => $domain->getLowerName(),
        ]))->render();
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return 'RouteServiceProvider';
    }

    /**
     * Get the destination file path.
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $generatorPath = GenerateConfigReader::read('provider');

        return $path . $generatorPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return mixed
     */
    protected function getWebRoutesPath()
    {
        return '/' . $this->laravel['domains']->config('stubs.files.routes/web', 'Routes/web.php');
    }

    /**
     * @return mixed
     */
    protected function getApiRoutesPath()
    {
        return '/' . $this->laravel['domains']->config('stubs.files.routes/api', 'Routes/api.php');
    }

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.provider.namespace') ?: $domain->config('paths.generator.provider.path', 'Providers');
    }

    /**
     * @return string
     */
    private function getControllerNameSpace(): string
    {
        $domain = $this->laravel['domains'];

        return str_replace('/', '\\', $domain->config('paths.generator.controller.namespace') ?: $domain->config('paths.generator.controller.path', 'Controller'));
    }
}
