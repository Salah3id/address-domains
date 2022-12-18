<?php
namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Salah3id\Domains\Repository\Generators\FileAlreadyExistsException;
use Salah3id\Domains\Repository\Generators\MigrationGenerator;
use Salah3id\Domains\Repository\Generators\ModelGenerator;
use Salah3id\Domains\Repository\Generators\ModelRelationsGenerator;
use Salah3id\Domains\Repository\Generators\RepositoryEloquentGenerator;
use Salah3id\Domains\Repository\Generators\RepositoryInterfaceGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Salah3id\Domains\Traits\DomainCommandTrait;

/**
 * Class RepositoryCommand
 * @package Salah3id\Domains\Commands
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryCommand extends Command
{

    use DomainCommandTrait;

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'domain:repository';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new repository.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * @var Collection
     */
    protected $generators = null;


    /**
     * Execute the command.
     *
     * @see fire()
     * @return void
     */
    public function handle(){
        $this->laravel->call([$this, 'fire'], func_get_args());
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function fire()
    {
        $this->generators = new Collection();
        if($this->argument('domain') && $this->argument('domain-path')) {
            $domain = $this->argument('domain');
            $domainPath = $this->argument('domain-path');
        } else {
            $domain = $this->getDomainNameForRepo();
            $domainPath = $this->laravel['domains']->getDomainPath($domain);
        }

        $migrationGenerator = new MigrationGenerator([
            'name'   => 'create_' . Str::snake(Str::plural($this->argument('name'))) . '_table',
            'fields' => $this->option('fillable'),
            'force'  => $this->option('force'),
        ],$domain,$domainPath);

        if (!$this->option('skip-migration')) {
            $this->generators->push($migrationGenerator);
        }

        $modelGenerator = new ModelGenerator([
            'name'     => $this->argument('name'),
            'fillable' => $this->option('fillable'),
            'force'    => $this->option('force')
        ],$domain,$domainPath);

        $modelRelationsGenerator = new ModelRelationsGenerator([
            'name' => $this->argument('name'),
            'force' => $this->option('force'),
        ],$domain,$domainPath);

        if (!$this->option('skip-model')) {
            $this->generators->push($modelGenerator);
            $this->generators->push($modelRelationsGenerator);
        }

        $this->generators->push(new RepositoryInterfaceGenerator([
            'name'  => $this->argument('name'),
            'force' => $this->option('force'),
        ],$domain,$domainPath));

        foreach ($this->generators as $generator) {
            $generator->run();
        }

        $model = $modelGenerator->getRootNamespace() . '\\' . $modelGenerator->getName();
        $model = str_replace([
            "\\",
            '/'
        ], '\\', $model);

        try {
            (new RepositoryEloquentGenerator([
                'name'      => $this->argument('name'),
                'rules'     => $this->option('rules'),
                'validator' => $this->option('validator'),
                'force'     => $this->option('force'),
                'model'     => $model
            ],$domain,$domainPath))->run();
            $this->info("Repository created successfully.");
        } catch (FileAlreadyExistsException $e) {
            $this->error($this->type . ' already exists!');

            return false;
        }
    }


    /**
     * The array of command arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return [
            [
                'name',
                InputArgument::REQUIRED,
                'The name of class being generated.',
                null
            ],
            ['domain', InputArgument::OPTIONAL, 'The name of domain will be used.'],
            ['domain-path', InputArgument::OPTIONAL, 'The path of domain will be used.'],
        ];
    }


    /**
     * The array of command options.
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            [
                'fillable',
                null,
                InputOption::VALUE_OPTIONAL,
                'The fillable attributes.',
                null
            ],
            [
                'rules',
                null,
                InputOption::VALUE_OPTIONAL,
                'The rules of validation attributes.',
                null
            ],
            [
                'validator',
                null,
                InputOption::VALUE_OPTIONAL,
                'Adds validator reference to the repository.',
                null
            ],
            [
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ],
            [
                'skip-migration',
                null,
                InputOption::VALUE_NONE,
                'Skip the creation of a migration file.',
                null,
            ],
            [
                'skip-model',
                null,
                InputOption::VALUE_NONE,
                'Skip the creation of a model.',
                null,
            ],
        ];
    }
}
