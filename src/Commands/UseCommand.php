<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;

class UseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:use';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Use the specified domain.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domain = Str::studly($this->argument('domain'));

        if (!$this->laravel['domains']->has($domain)) {
            $this->error("Domain [{$domain}] does not exists.");

            return E_ERROR;
        }

        $this->laravel['domains']->setUsed($domain);

        $this->info("Domain [{$domain}] used successfully.");

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['domain', InputArgument::REQUIRED, 'The name of domain will be used.'],
        ];
    }
}
