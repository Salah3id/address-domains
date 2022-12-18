<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Domain;
use Symfony\Component\Console\Input\InputOption;

class ListCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show list of all domains.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->components->twoColumnDetail('<fg=gray>Status / Name</>', '<fg=gray>Path / priority</>');
        collect($this->getRows())->each(function ($row) {

            $this->components->twoColumnDetail("[{$row[1]}] {$row[0]}", "{$row[3]} [{$row[2]}]");
        });

        return 0;
    }

    /**
     * Get table rows.
     *
     * @return array
     */
    public function getRows()
    {
        $rows = [];

        /** @var Domain $domain */
        foreach ($this->getDomains() as $domain) {
            $rows[] = [
                $domain->getName(),
                $domain->isEnabled() ? '<fg=green>Enabled</>' : '<fg=red>Disabled</>',
                $domain->get('priority'),
                $domain->getPath(),
            ];
        }

        return $rows;
    }

    public function getDomains()
    {
        switch ($this->option('only')) {
            case 'enabled':
                return $this->laravel['domains']->getByStatus(1);

                break;

            case 'disabled':
                return $this->laravel['domains']->getByStatus(0);

                break;

            case 'priority':
                return $this->laravel['domains']->getPriority($this->option('direction'));

                break;

            default:
                return $this->laravel['domains']->all();

                break;
        }
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['only', 'o', InputOption::VALUE_OPTIONAL, 'Types of domains will be displayed.', null],
            ['direction', 'd', InputOption::VALUE_OPTIONAL, 'The direction of ordering.', 'asc'],
        ];
    }
}
