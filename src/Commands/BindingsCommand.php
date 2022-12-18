<?php
namespace Salah3id\Domains\Commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Salah3id\Domains\Repository\Generators\BindingsGenerator;
use Salah3id\Domains\Repository\Generators\FileAlreadyExistsException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Salah3id\Domains\Traits\DomainCommandTrait;

/**
 * Class BindingsCommand
 * @package Salah3id\Domains\Commands
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class BindingsCommand extends Command
{

    use DomainCommandTrait;

    /**
     * The name of command.
     *
     * @var string
     */
    protected $name = 'domain:repo-bindings';

    /**
     * The description of command.
     *
     * @var string
     */
    protected $description = 'Add repository bindings to service provider.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Bindings';

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
            $bindingGenerator = new BindingsGenerator([
                'name' => $this->argument('name'),
                'force' => $this->option('force'),
            ],$domain,$domainPath);
            // generate repository service provider
            if (!file_exists($bindingGenerator->getPath())) {
                $this->call('domain:make-provider', [
                    'name' => $bindingGenerator->getConfigGeneratorClassPath($bindingGenerator->getPathConfigNode()),
                    'domain' => $domain
                ]);
                // placeholder to mark the place in file where to prepend repository bindings
                $provider = File::get($bindingGenerator->getPath());
                File::put($bindingGenerator->getPath(), vsprintf(str_replace('//', '%s', $provider), [
                    '//',
                    $bindingGenerator->bindPlaceholder
                ]));
            }
            $bindingGenerator->run();
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
