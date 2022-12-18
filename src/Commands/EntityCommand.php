<?php
namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Salah3id\Domains\Traits\DomainCommandTrait;

/**
 * Class EntityCommand
 * @package Salah3id\Domains\Commands
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class EntityCommand extends Command
{

    use DomainCommandTrait;
    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'domain:entity';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new entity.';

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
        $domain = $this->getDomainNameForRepo();
        $domainPath = $this->laravel['domains']->getDomainPath($domain);

        if ($this->confirm('Would you like to create a Presenter? [y|N]')) {
            $this->call('domain:presenter', [
                'name'    => $this->argument('name'),
                '--force' => $this->option('force'),
                'domain' => $domain,
                'domain-path' => $domainPath
            ]);
        }

        $validator = $this->option('validator');
        if (is_null($validator) && $this->confirm('Would you like to create a Validator? [y|N]')) {
            $validator = 'yes';
        }

        if ($validator == 'yes') {
            $this->call('domain:validator', [
                'name'    => $this->argument('name'),
                '--rules' => $this->option('rules'),
                '--force' => $this->option('force'),
                'domain' => $domain,
                'domain-path' => $domainPath
            ]);
        }

        if ($this->confirm('Would you like to create a Controller? [y|N]')) {

            $resource_args = [
                'name'    => $this->argument('name'),
                'domain' => $domain,
                'domain-path' => $domainPath
            ];

            // Generate a controller resource
            $controller_command = ((float) app()->version() >= 5.5  ? 'make:rest-controller' : 'make:resource');
            $this->call($controller_command, $resource_args);
        }

        $this->call('domain:repository', [
            'name'        => $this->argument('name'),
            '--fillable'  => $this->option('fillable'),
            '--rules'     => $this->option('rules'),
            '--validator' => $validator,
            '--force'     => $this->option('force'),
            'domain' => $domain,
            'domain-path' => $domainPath
        ]);

        $this->call('domain:repo-bindings', [
            'name'    => $this->argument('name'),
            '--force' => $this->option('force'),
            'domain' => $domain,
            'domain-path' => $domainPath
        ]);
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
            ]
        ];
    }
}
