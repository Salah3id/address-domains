<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;

class MiddlewareMakeCommand extends GeneratorCommand
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
    protected $name = 'domain:make-middleware';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new middleware class for the specified domain.';

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.filter.namespace') ?: $domain->config('paths.generator.filter.path', 'Http/Middleware');
    }

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
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub('/middleware.stub', [
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

        $middlewarePath = GenerateConfigReader::read('filter');

        return $path . $middlewarePath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * Run the command.
     */
    public function handle(): int
    {

        $this->components->info('Creating middleware...');

        parent::handle();

        return 0;
    }
}
