<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Support\Str;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Support\Stub;
use Salah3id\Domains\Traits\CanClearDomainsCache;
use Salah3id\Domains\Traits\DomainCommandTrait;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedMakeCommand extends GeneratorCommand
{
    use DomainCommandTrait;
    use CanClearDomainsCache;

    protected $argumentName = 'name';

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:make-seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate new seeder for the specified domain.';

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of seeder will be created.'],
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
            [
                'master',
                null,
                InputOption::VALUE_NONE,
                'Indicates the seeder will created is a master database seeder.',
            ],
        ];
    }

    /**
     * @return mixed
     */
    protected function getTemplateContents()
    {
        $domain = $this->laravel['domains']->findOrFail($this->getDomainName());

        return (new Stub('/seeder.stub', [
            'NAME' => $this->getSeederName(),
            'DOMAIN' => $this->getDomainName(),
            'NAMESPACE' => $this->getClassNamespace($domain),

        ]))->render();
    }

    /**
     * @return mixed
     */
    protected function getDestinationFilePath()
    {
        $this->clearCache();

        $path = $this->laravel['domains']->getDomainPath($this->getDomainName());

        $seederPath = GenerateConfigReader::read('seeder');

        return $path . $seederPath->getPath() . '/' . $this->getSeederName() . '.php';
    }

    /**
     * Get seeder name.
     *
     * @return string
     */
    private function getSeederName()
    {
        $end = $this->option('master') ? 'DatabaseSeeder' : 'TableSeeder';

        return Str::studly($this->argument('name')) . $end;
    }

    /**
     * Get default namespace.
     *
     * @return string
     */
    public function getDefaultNamespace(): string
    {
        $domain = $this->laravel['domains'];

        return $domain->config('paths.generator.seeder.namespace') ?: $domain->config('paths.generator.seeder.path', 'Database/Seeders');
    }
}
