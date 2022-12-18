<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Json;
use Salah3id\Domains\Process\Installer;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the specified domain by given package name (vendor/name).';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (is_null($this->argument('name'))) {
            return $this->installFromFile();
        }

        $this->install(
            $this->argument('name'),
            $this->argument('version'),
            $this->option('type'),
            $this->option('tree')
        );

        return 0;
    }

    /**
     * Install domains from domains.json file.
     */
    protected function installFromFile(): int
    {
        if (!file_exists($path = base_path('domains.json'))) {
            $this->error("File 'domains.json' does not exist in your project root.");

            return E_ERROR;
        }

        $domains = Json::make($path);

        $dependencies = $domains->get('require', []);

        foreach ($dependencies as $domain) {
            $domain = collect($domain);

            $this->install(
                $domain->get('name'),
                $domain->get('version'),
                $domain->get('type')
            );
        }

        return 0;
    }

    /**
     * Install the specified domain.
     *
     * @param string $name
     * @param string $version
     * @param string $type
     * @param bool   $tree
     */
    protected function install($name, $version = 'dev-master', $type = 'composer', $tree = false)
    {
        $installer = new Installer(
            $name,
            $version,
            $type ?: $this->option('type'),
            $tree ?: $this->option('tree')
        );

        $installer->setRepository($this->laravel['domains']);

        $installer->setConsole($this);

        if ($timeout = $this->option('timeout')) {
            $installer->setTimeout($timeout);
        }

        if ($path = $this->option('path')) {
            $installer->setPath($path);
        }

        $installer->run();

        if (!$this->option('no-update')) {
            $this->call('domain:update', [
                'domain' => $installer->getDomainName(),
            ]);
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::OPTIONAL, 'The name of domain will be installed.'],
            ['version', InputArgument::OPTIONAL, 'The version of domain will be installed.'],
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
            ['timeout', null, InputOption::VALUE_OPTIONAL, 'The process timeout.', null],
            ['path', null, InputOption::VALUE_OPTIONAL, 'The installation path.', null],
            ['type', null, InputOption::VALUE_OPTIONAL, 'The type of installation.', null],
            ['tree', null, InputOption::VALUE_NONE, 'Install the domain as a git subtree', null],
            ['no-update', null, InputOption::VALUE_NONE, 'Disables the automatic update of the dependencies.', null],
        ];
    }
}
