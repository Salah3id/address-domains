<?php

namespace Salah3id\Domains\Activators;

use Illuminate\Cache\CacheManager;
use Illuminate\Config\Repository as Config;
use Illuminate\Container\Container;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use Salah3id\Domains\Contracts\ActivatorInterface;
use Salah3id\Domains\Domain;

class FileActivator implements ActivatorInterface
{
    /**
     * Laravel cache instance
     *
     * @var CacheManager
     */
    private $cache;

    /**
     * Laravel Filesystem instance
     *
     * @var Filesystem
     */
    private $files;

    /**
     * Laravel config instance
     *
     * @var Config
     */
    private $config;

    /**
     * @var string
     */
    private $cacheKey;

    /**
     * @var string
     */
    private $cacheLifetime;

    /**
     * Array of domains activation statuses
     *
     * @var array
     */
    private $domainsStatuses;

    /**
     * File used to store activation statuses
     *
     * @var string
     */
    private $statusesFile;

    public function __construct(Container $app)
    {
        $this->cache = $app['cache'];
        $this->files = $app['files'];
        $this->config = $app['config'];
        $this->statusesFile = $this->config('statuses-file');
        $this->cacheKey = $this->config('cache-key');
        $this->cacheLifetime = $this->config('cache-lifetime');
        $this->domainsStatuses = $this->getDomainsStatuses();
    }

    /**
     * Get the path of the file where statuses are stored
     *
     * @return string
     */
    public function getStatusesFilePath(): string
    {
        return $this->statusesFile;
    }

    /**
     * @inheritDoc
     */
    public function reset(): void
    {
        if ($this->files->exists($this->statusesFile)) {
            $this->files->delete($this->statusesFile);
        }
        $this->domainsStatuses = [];
        $this->flushCache();
    }

    /**
     * @inheritDoc
     */
    public function enable(Domain $domain): void
    {
        $this->setActiveByName($domain->getName(), true);
    }

    /**
     * @inheritDoc
     */
    public function disable(Domain $domain): void
    {
        $this->setActiveByName($domain->getName(), false);
    }

    /**
     * @inheritDoc
     */
    public function hasStatus(Domain $domain, bool $status): bool
    {
        if (!isset($this->domainsStatuses[$domain->getName()])) {
            return $status === false;
        }

        return $this->domainsStatuses[$domain->getName()] === $status;
    }

    /**
     * @inheritDoc
     */
    public function setActive(Domain $domain, bool $active): void
    {
        $this->setActiveByName($domain->getName(), $active);
    }

    /**
     * @inheritDoc
     */
    public function setActiveByName(string $name, bool $status): void
    {
        $this->domainsStatuses[$name] = $status;
        $this->writeJson();
        $this->flushCache();
    }

    /**
     * @inheritDoc
     */
    public function delete(Domain $domain): void
    {
        if (!isset($this->domainsStatuses[$domain->getName()])) {
            return;
        }
        unset($this->domainsStatuses[$domain->getName()]);
        $this->writeJson();
        $this->flushCache();
    }

    /**
     * Writes the activation statuses in a file, as json
     */
    private function writeJson(): void
    {
        $this->files->put($this->statusesFile, json_encode($this->domainsStatuses, JSON_PRETTY_PRINT));
    }

    /**
     * Reads the json file that contains the activation statuses.
     * @return array
     * @throws FileNotFoundException
     */
    private function readJson(): array
    {
        if (!$this->files->exists($this->statusesFile)) {
            return [];
        }

        return json_decode($this->files->get($this->statusesFile), true);
    }

    /**
     * Get domains statuses, either from the cache or from
     * the json statuses file if the cache is disabled.
     * @return array
     * @throws FileNotFoundException
     */
    private function getDomainsStatuses(): array
    {
        if (!$this->config->get('domains.cache.enabled')) {
            return $this->readJson();
        }

        return $this->cache->store($this->config->get('domains.cache.driver'))->remember($this->cacheKey, $this->cacheLifetime, function () {
            return $this->readJson();
        });
    }

    /**
     * Reads a config parameter under the 'activators.file' key
     *
     * @param  string $key
     * @param  $default
     * @return mixed
     */
    private function config(string $key, $default = null)
    {
        return $this->config->get('domains.activators.file.' . $key, $default);
    }

    /**
     * Flushes the domains activation statuses cache
     */
    private function flushCache(): void
    {
        $this->cache->store($this->config->get('domains.cache.driver'))->forget($this->cacheKey);
    }
}
