<?php
namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Salah3id\Domains\Repository\Generators\FileAlreadyExistsException;
use Salah3id\Domains\Repository\Generators\ValidatorGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Salah3id\Domains\Traits\DomainCommandTrait;



/**
 * Class ValidatorCommand
 * @package Salah3id\Domains\Commands
 */
class ValidatorCommand extends Command
{

    use DomainCommandTrait;

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'domain:validator';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new validator.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Validator';


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
            (new ValidatorGenerator([
                'name' => $this->argument('name'),
                'rules' => $this->option('rules'),
                'force' => $this->option('force'),
            ],$domain,$domainPath))->run();
            $this->info("Validator created successfully.");
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
                'The name of model for which the validator is being generated.',
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
                'rules',
                null,
                InputOption::VALUE_OPTIONAL,
                'The rules of validation attributes.',
                null
            ],
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
