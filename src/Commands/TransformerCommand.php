<?php
namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Salah3id\Domains\Repository\Generators\FileAlreadyExistsException;
use Salah3id\Domains\Repository\Generators\TransformerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Salah3id\Domains\Traits\DomainCommandTrait;

/**
 * Class TransformerCommand
 * @package Salah3id\Domains\Commands
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class TransformerCommand extends Command
{

    use DomainCommandTrait;
    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'domain:transformer';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new transformer.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Transformer';

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
        try {
            (new TransformerGenerator([
                'name' => $this->argument('name'),
                'force' => $this->option('force'),
            ],$domain,$domainPath))->run();
            $this->info("Transformer created successfully.");
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
                'The name of model for which the transformer is being generated.',
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
                'force',
                'f',
                InputOption::VALUE_NONE,
                'Force the creation if file already exists.',
                null
            ]
        ];
    }
}
