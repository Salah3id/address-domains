<?php

namespace Salah3id\Domains\Traits;

trait MigrationLoaderTrait
{
    /**
     * Include all migrations files from the specified domain.
     *
     * @param string $domain
     */
    protected function loadMigrationFiles($domain)
    {
        $path = $this->laravel['domains']->getDomainPath($domain) . $this->getMigrationGeneratorPath();

        $files = $this->laravel['files']->glob($path . '/*_*.php');

        foreach ($files as $file) {
            $this->laravel['files']->requireOnce($file);
        }
    }

    /**
     * Get migration generator path.
     *
     * @return string
     */
    protected function getMigrationGeneratorPath()
    {
        return $this->laravel['domains']->config('paths.generator.migration');
    }
}
