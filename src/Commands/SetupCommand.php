<?php

namespace Salah3id\Domains\Commands;

use Illuminate\Console\Command;

class SetupCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'domain:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setting up domains folders for first use.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $code = $this->generateDomainsFolder();

        return $this->generateAssetsFolder() | $code;
    }

    /**
     * Generate the domains folder.
     */
    public function generateDomainsFolder()
    {
        return $this->generateDirectory(
            $this->laravel['domains']->config('paths.domains'),
            'Domains directory created successfully',
            'Domains directory already exist'
        );
    }

    /**
     * Generate the assets folder.
     */
    public function generateAssetsFolder()
    {
        return $this->generateDirectory(
            $this->laravel['domains']->config('paths.assets'),
            'Assets directory created successfully',
            'Assets directory already exist'
        );
    }

    /**
     * Generate the specified directory by given $dir.
     *
     * @param $dir
     * @param $success
     * @param $error
     * @return int
     */
    protected function generateDirectory($dir, $success, $error): int
    {
        if (!$this->laravel['files']->isDirectory($dir)) {
            $this->laravel['files']->makeDirectory($dir, 0755, true, true);

            $this->components->info($success);

            return 0;
        }

        $this->components->error($error);

        return E_ERROR;
    }
}
