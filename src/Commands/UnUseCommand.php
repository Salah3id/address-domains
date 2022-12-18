<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;

class UnUseCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:unuse';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Forget the used domain with domain:use';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->laravel['domains']->forgetUsed();

        $this->components->info('Previous domain used successfully forgotten.');

        return 0;
    }
}
