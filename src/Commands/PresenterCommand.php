<?php
namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Repository\Generators\FileAlreadyExistsException;
use Salah3id\Domains\Repository\Generators\PresenterGenerator;
use Salah3id\Domains\Repository\Generators\TransformerGenerator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Salah3id\Domains\Traits\DomainCommandTrait;


/**
 * Class PresenterCommand
 * @package Salah3id\Domains\Commands
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class PresenterCommand extends Command
{
    use DomainCommandTrait;

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'domain:presenter';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Create a new presenter.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Presenter';

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

        try {
            if($this->argument('domain') && $this->argument('domain-path')) {
                $domain = $this->argument('domain');
                $domainPath = $this->argument('domain-path');
            } else {
                $domain = $this->getDomainNameForRepo();
                $domainPath = $this->laravel['domains']->getDomainPath($domain);
            }
            
            (new PresenterGenerator([
                'name'  => $this->argument('name'),
                'force' => $this->option('force'),
            ],$domain,$domainPath))->run();
            $this->info("Presenter created successfully.");

            if (!\File::exists($domainPath . '/Transformers/' . $this->argument('name') .'\\'. $this->argument('name') .'Resource.php')) {
                if ($this->confirm('Would you like to create a Transformer? [y|N]')) {
                    (new TransformerGenerator([
                        'name'  => $this->argument('name'),
                        'force' => $this->option('force'),
                    ],$domain,$domainPath))->run();
                    $this->info("Transformer created successfully.");
                }
            }
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
                'The name of model for which the presenter is being generated.',
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
            ]
        ];
    }
}
