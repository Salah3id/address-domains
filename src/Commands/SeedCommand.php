<?php

namespace Salah3id\Domains\Commands;

use ErrorException;
use Illuminate\Console\Command;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Str;
use Salah3id\Domains\Contracts\RepositoryInterface;
use Salah3id\Domains\Domain;
use Salah3id\Domains\Support\Config\GenerateConfigReader;
use Salah3id\Domains\Traits\DomainCommandTrait;
use RuntimeException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class SeedCommand extends Command
{
    use DomainCommandTrait;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run database seeder from the specified domain or from all domains.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            if ($name = $this->argument('domain')) {
                $name = Str::studly($name);
                $this->domainSeed($this->getDomainByName($name));
            } else {
                $domains = $this->getDomainRepository()->getOrdered();
                array_walk($domains, [$this, 'domainSeed']);
                $this->info('All domains seeded.');
            }
        } catch (\Error $e) {
            $e = new ErrorException($e->getMessage(), $e->getCode(), 1, $e->getFile(), $e->getLine(), $e);
            $this->reportException($e);
            $this->renderException($this->getOutput(), $e);

            return E_ERROR;
        } catch (\Exception $e) {
            $this->reportException($e);
            $this->renderException($this->getOutput(), $e);

            return E_ERROR;
        }

        return 0;
    }

    /**
     * @throws RuntimeException
     * @return RepositoryInterface
     */
    public function getDomainRepository(): RepositoryInterface
    {
        $domains = $this->laravel['domains'];
        if (!$domains instanceof RepositoryInterface) {
            throw new RuntimeException('Domain repository not found!');
        }

        return $domains;
    }

    /**
     * @param $name
     *
     * @throws RuntimeException
     *
     * @return Domain
     */
    public function getDomainByName($name)
    {
        $domains = $this->getDomainRepository();
        if ($domains->has($name) === false) {
            throw new RuntimeException("Domain [$name] does not exists.");
        }

        return $domains->find($name);
    }

    /**
     * @param Domain $domain
     *
     * @return void
     */
    public function domainSeed(Domain $domain)
    {
        $seeders = [];
        $name = $domain->getName();
        $config = $domain->get('migration');
        if (is_array($config) && array_key_exists('seeds', $config)) {
            foreach ((array)$config['seeds'] as $class) {
                if (class_exists($class)) {
                    $seeders[] = $class;
                }
            }
        } else {
            $class = $this->getSeederName($name); //legacy support
            if (class_exists($class)) {
                $seeders[] = $class;
            } else {
                //look at other namespaces
                $classes = $this->getSeederNames($name);
                foreach ($classes as $class) {
                    if (class_exists($class)) {
                        $seeders[] = $class;
                    }
                }
            }
        }

        if (count($seeders) > 0) {
            array_walk($seeders, [$this, 'dbSeed']);
            $this->info("Domain [$name] seeded.");
        }
    }

    /**
     * Seed the specified domain.
     *
     * @param string $className
     */
    protected function dbSeed($className)
    {
        if ($option = $this->option('class')) {
            $params['--class'] = Str::finish(substr($className, 0, strrpos($className, '\\')), '\\') . $option;
        } else {
            $params = ['--class' => $className];
        }

        if ($option = $this->option('database')) {
            $params['--database'] = $option;
        }

        if ($option = $this->option('force')) {
            $params['--force'] = $option;
        }

        $this->call('db:seed', $params);
    }

    /**
     * Get master database seeder name for the specified domain.
     *
     * @param string $name
     *
     * @return string
     */
    public function getSeederName($name)
    {
        $name = Str::studly($name);

        $namespace = $this->laravel['domains']->config('namespace');
        $config = GenerateConfigReader::read('seeder');
        $seederPath = str_replace('/', '\\', $config->getPath());

        return $namespace . '\\' . $name . '\\' . $seederPath . '\\' . $name . 'DatabaseSeeder';
    }

    /**
     * Get master database seeder name for the specified domain under a different namespace than Domains.
     *
     * @param string $name
     *
     * @return array $foundDomains array containing namespace paths
     */
    public function getSeederNames($name)
    {
        $name = Str::studly($name);

        $seederPath = GenerateConfigReader::read('seeder');
        $seederPath = str_replace('/', '\\', $seederPath->getPath());

        $foundDomains = [];
        foreach ($this->laravel['domains']->config('scan.paths') as $path) {
            $namespace = array_slice(explode('/', $path), -1)[0];
            $foundDomains[] = $namespace . '\\' . $name . '\\' . $seederPath . '\\' . $name . 'DatabaseSeeder';
        }

        return $foundDomains;
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @param  \Throwable  $e
     * @return void
     */
    protected function renderException($output, \Exception $e)
    {
        $this->laravel[ExceptionHandler::class]->renderForConsole($output, $e);
    }

    /**
     * Report the exception to the exception handler.
     *
     * @param  \Throwable  $e
     * @return void
     */
    protected function reportException(\Exception $e)
    {
        $this->laravel[ExceptionHandler::class]->report($e);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
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
            ['class', null, InputOption::VALUE_OPTIONAL, 'The class name of the root seeder.'],
            ['database', null, InputOption::VALUE_OPTIONAL, 'The database connection to seed.'],
            ['force', null, InputOption::VALUE_NONE, 'Force the operation to run when in production.'],
        ];
    }
}
