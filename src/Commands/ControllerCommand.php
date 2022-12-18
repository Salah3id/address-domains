<?php
namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Salah3id\Domains\Repository\Generators\ControllerGenerator;
use Salah3id\Domains\Repository\Generators\FileAlreadyExistsException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Salah3id\Domains\Traits\DomainCommandTrait;

/**
 * Class ControllerCommand
 * @package Salah3id\Domains\Commands
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class ControllerCommand extends Command
{

    use DomainCommandTrait;

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'domain:resource';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new RESTful controller.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * ControllerCommand constructor.
     */
    public function __construct()
    {
        $this->name = ((float) app()->version() >= 5.5  ? 'make:rest-controller' : 'make:resource');
        parent::__construct();
    }

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
        if($this->argument('domain') && $this->argument('domain-path')) {
            $domain = $this->argument('domain');
            $domainPath = $this->argument('domain-path');
            
        } else {
            $domain = $this->getDomainNameForRepo();
            $domainPath = $this->laravel['domains']->getDomainPath($domain);
        }
        try {
            $this->call('domain:make-request', [
                'name' => 'CreateRequests\\' . ucfirst($this->argument('name')) . 'CreateRequest',
                'domain' => $domain
            ]);

            // Generate update request for controller
            $this->call('domain:make-request', [
                'name' => 'UpdateRequests\\' . ucfirst($this->argument('name')) . 'UpdateRequest',
                'domain' => $domain
            ]);

            (new ControllerGenerator([
                'name' => $this->argument('name'),
                'force' => $this->option('force'),
            ],$domain,$domainPath))->run();

            $this->info($this->type . ' created successfully.');

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
                'The name of model for which the controller is being generated.',
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
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ],
        ];
    }
}
