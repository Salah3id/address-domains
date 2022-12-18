<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Contracts\ActivatorInterface;
use Salah3id\Domains\Generators\DomainGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class DomainMakeCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new domain.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $names = $this->argument('name');
        $success = true;

        foreach ($names as $name) {
            $code = with(new DomainGenerator($name))
                ->setFilesystem($this->laravel['files'])
                ->setDomain($this->laravel['domains'])
                ->setConfig($this->laravel['config'])
                ->setActivator($this->laravel[ActivatorInterface::class])
                ->setConsole($this)
                ->setComponent($this->components)
                ->setForce($this->option('force'))
                ->setType($this->getDomainType())
                ->setActive(!$this->option('disabled'))
                ->generate();

            if ($code === E_ERROR) {
                $success = false;
            }
        }

        return $success ? 0 : E_ERROR;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::IS_ARRAY, 'The names of domains will be created.'],
        ];
    }

    protected function getOptions()
    {
        return [
            ['plain', 'p', InputOption::VALUE_NONE, 'Generate a plain domain (without some resources).'],
            ['api', null, InputOption::VALUE_NONE, 'Generate an api domain.'],
            ['web', null, InputOption::VALUE_NONE, 'Generate a web domain.'],
            ['disabled', 'd', InputOption::VALUE_NONE, 'Do not enable the domain at creation.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when the domain already exists.'],
        ];
    }

    /**
    * Get domain type .
    *
    * @return string
    */
    private function getDomainType()
    {
        $isPlain = $this->option('plain');
        $isApi = $this->option('api');

        if ($isPlain && $isApi) {
            return 'web';
        }
        if ($isPlain) {
            return 'plain';
        } elseif ($isApi) {
            return 'api';
        } else {
            return 'web';
        }
    }
}
