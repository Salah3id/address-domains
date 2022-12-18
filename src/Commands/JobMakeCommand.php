<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class JobMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:make-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new job class for the specified domain';

    protected $argumentName = 'name';

    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.jobs.namespace') ?: $domain->config('paths.generator.jobs.path', 'Jobs');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the job.'],
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
            ['sync', null, InputOption::VALUE_NONE, 'Indicates that job should be synchronous.'],
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

        return (new Stub($this->getStubName(), [
            'NAMESPACE' => $this->getClassNamespace($domain),
            'CLASS'     => $this->getClass(),
        ]))->render();
    }

    /**
     * Get the destination file path.
     *
     * @return string
     */
    protected function getDestinationFilePath()
    {
        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $jobPath = GenerateConfigReader::read('jobs');

        return $path . $jobPath->getPath() . '/' . $this->getFileName() . '.php';
    }

    /**
     * @return string
     */
    private function getFileName()
    {
        return Str::studly($this->argument('name'));
    }

    /**
     * @return string
     */
    protected function getStubName(): string
    {
        if ($this->option('sync')) {
            return '/job.stub';
        }

        return '/job-queued.stub';
    }
}
