<?php

declare(strict_types=1);

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Salah3id\Domains\Contracts\RepositoryInterface;
use Salah3id\Domains\Domain;

class LaravelDomainsV6Migrator extends Command
{
    protected $name = 'domain:v6:migrate';
    protected $description = 'Migrate address-domains v5 domains statuses to v6.';

    public function handle(): int
    {
        $domainStatuses = [];
        /** @var RepositoryInterface $domains */
        $domains = $this->laravel['domains'];

        $domains = $domains->all();
        /** @var Domain $domain */
        foreach ($domains as $domain) {
            if ($domain->json()->get('active') === 1) {
                $domain->enable();
                $domainStatuses[] = [$domain->getName(), 'Enabled'];
            }
            if ($domain->json()->get('active') === 0) {
                $domain->disable();
                $domainStatuses[] = [$domain->getName(), 'Disabled'];
            }
        }
        $this->info('All domains have been migrated.');
        $this->table(['Domain name', 'Status'], $domainStatuses);

        return 0;
    }
}
