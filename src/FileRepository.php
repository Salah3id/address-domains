<?php

namespace Salah3id\Domains;

use Countable;
use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Illuminate\Contracts\Routing\UrlGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Salah3id\Domains\Contracts\RepositoryInterface;
use Salah3id\Domains\Exceptions\InvalidAssetPath;
use Salah3id\Domains\Exceptions\DomainNotFoundException;
use Salah3id\Domains\Process\Installer;
use Salah3id\Domains\Process\Updater;

abstract class FileRepository implements RepositoryInterface, Countable
{
    use Macroable;

    /**
     * Application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    /**
     * The domain path.
     *
     * @var string|null
     */
    protected $path;

    /**
     * The scanned paths.
     *
     * @var array
     */
    protected $paths = [];

    /**
     * @var string
     */
    protected $stubPath;
    /**
     * @var UrlGenerator
     */
    private $url;
    /**
     * @var ConfigRepository
     */
    private $config;
    /**
     * @var Filesystem
     */
    private $files;
    /**
     * @var CacheManager
     */
    private $cache;

    /**
     * The constructor.
     * @param Container $app
     * @param string|null $path
     */
    public function __construct(Container $app, $path = null)
    {
        $this->app = $app;
        $this->path = $path;
        $this->url = $app['url'];
        $this->config = $app['config'];
        $this->files = $app['files'];
        $this->cache = $app['cache'];
    }

    /**
     * Add other domain location.
     *
     * @param string $path
     *
     * @return $this
     */
    public function addLocation($path)
    {
        $this->paths[] = $path;

        return $this;
    }

    /**
     * Get all additional paths.
     *
     * @return array
     */
    public function getPaths(): array
    {
        return $this->paths;
    }

    /**
     * Get scanned domains paths.
     *
     * @return array
     */
    public function getScanPaths(): array
    {
        $paths = $this->paths;

        $paths[] = $this->getPath();

        if ($this->config('scan.enabled')) {
            $paths = array_merge($paths, $this->config('scan.paths'));
        }

        $paths = array_map(function ($path) {
            return Str::endsWith($path, '/*') ? $path : Str::finish($path, '/*');
        }, $paths);

        return $paths;
    }

    /**
     * Creates a new Domain instance
     *
     * @param Container $app
     * @param string $args
     * @param string $path
     * @return \Salah3id\Domains\Domain
     */
    abstract protected function createDomain(...$args);

    /**
     * Get & scan all domains.
     *
     * @return array
     */
    public function scan()
    {
        $paths = $this->getScanPaths();

        $domains = [];

        foreach ($paths as $key => $path) {
            $manifests = $this->getFiles()->glob("{$path}/domain.json");

            is_array($manifests) || $manifests = [];

            foreach ($manifests as $manifest) {
                $name = Json::make($manifest)->get('name');

                $domains[$name] = $this->createDomain($this->app, $name, dirname($manifest));
            }
        }

        return $domains;
    }

    /**
     * Get all domains.
     *
     * @return array
     */
    public function all(): array
    {
        if (!$this->config('cache.enabled')) {
            return $this->scan();
        }

        return $this->formatCached($this->getCached());
    }

    /**
     * Format the cached data as array of domains.
     *
     * @param array $cached
     *
     * @return array
     */
    protected function formatCached($cached)
    {
        $domains = [];

        foreach ($cached as $name => $domain) {
            $path = $domain['path'];

            $domains[$name] = $this->createDomain($this->app, $name, $path);
        }

        return $domains;
    }

    /**
     * Get cached domains.
     *
     * @return array
     */
    public function getCached()
    {
        return $this->cache->store($this->config->get('domains.cache.driver'))->remember($this->config('cache.key'), $this->config('cache.lifetime'), function () {
            return $this->toCollection()->toArray();
        });
    }

    /**
     * Get all domains as collection instance.
     *
     * @return Collection
     */
    public function toCollection(): Collection
    {
        return new Collection($this->scan());
    }

    /**
     * Get domains by status.
     *
     * @param $status
     *
     * @return array
     */
    public function getByStatus($status): array
    {
        $domains = [];

        /** @var Domain $domain */
        foreach ($this->all() as $name => $domain) {
            if ($domain->isStatus($status)) {
                $domains[$name] = $domain;
            }
        }

        return $domains;
    }

    /**
     * Determine whether the given domain exist.
     *
     * @param $name
     *
     * @return bool
     */
    public function has($name): bool
    {
        return array_key_exists($name, $this->all());
    }

    /**
     * Get list of enabled domains.
     *
     * @return array
     */
    public function allEnabled(): array
    {
        return $this->getByStatus(true);
    }

    /**
     * Get list of disabled domains.
     *
     * @return array
     */
    public function allDisabled(): array
    {
        return $this->getByStatus(false);
    }

    /**
     * Get count from all domains.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->all());
    }

    /**
     * Get all ordered domains.
     *
     * @param string $direction
     *
     * @return array
     */
    public function getOrdered($direction = 'asc'): array
    {
        $domains = $this->allEnabled();

        uasort($domains, function (Domain $a, Domain $b) use ($direction) {
            if ($a->get('priority') === $b->get('priority')) {
                return 0;
            }

            if ($direction === 'desc') {
                return $a->get('priority') < $b->get('priority') ? 1 : -1;
            }

            return $a->get('priority') > $b->get('priority') ? 1 : -1;
        });

        return $domains;
    }

    /**
     * @inheritDoc
     */
    public function getPath(): string
    {
        return $this->path ?: $this->config('paths.domains', base_path('Domains'));
    }

    /**
     * @inheritDoc
     */
    public function register(): void
    {
        foreach ($this->getOrdered() as $domain) {
            $domain->register();
        }
    }

    /**
     * @inheritDoc
     */
    public function boot(): void
    {
        foreach ($this->getOrdered() as $domain) {
            $domain->boot();
        }
    }

    /**
     * @inheritDoc
     */
    public function find(string $name)
    {
        foreach ($this->all() as $domain) {
            if ($domain->getLowerName() === strtolower($name)) {
                return $domain;
            }
        }

        return;
    }

    /**
     * Find a specific domain, if there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return Domain
     *
     * @throws DomainNotFoundException
     */
    public function findOrFail(string $name)
    {
        $domain = $this->find($name);

        if ($domain !== null) {
            return $domain;
        }

        throw new DomainNotFoundException("Domain [{$name}] does not exist!");
    }

    /**
     * Get all domains as laravel collection instance.
     *
     * @param $status
     *
     * @return Collection
     */
    public function collections($status = 1): Collection
    {
        return new Collection($this->getByStatus($status));
    }

    /**
     * Get domain path for a specific domain.
     *
     * @param $domain
     *
     * @return string
     */
    public function getDomainPath($domain)
    {
        try {
            return $this->findOrFail($domain)->getPath() . '/';
        } catch (DomainNotFoundException $e) {
            return $this->getPath() . '/' . Str::studly($domain) . '/';
        }
    }

    /**
     * @inheritDoc
     */
    public function assetPath(string $domain): string
    {
        return $this->config('paths.assets') . '/' . $domain;
    }

    /**
     * @inheritDoc
     */
    public function config(string $key, $default = null)
    {
        return $this->config->get('domains.' . $key, $default);
    }

    /**
     * Get storage path for domain used.
     *
     * @return string
     */
    public function getUsedStoragePath(): string
    {
        $directory = storage_path('app/domains');
        if ($this->getFiles()->exists($directory) === false) {
            $this->getFiles()->makeDirectory($directory, 0777, true);
        }

        $path = storage_path('app/domains/domains.used');
        if (!$this->getFiles()->exists($path)) {
            $this->getFiles()->put($path, '');
        }

        return $path;
    }

    /**
     * Set domain used for cli session.
     *
     * @param $name
     *
     * @throws DomainNotFoundException
     */
    public function setUsed($name)
    {
        $domain = $this->findOrFail($name);

        $this->getFiles()->put($this->getUsedStoragePath(), $domain);
    }

    /**
     * Forget the domain used for cli session.
     */
    public function forgetUsed()
    {
        if ($this->getFiles()->exists($this->getUsedStoragePath())) {
            $this->getFiles()->delete($this->getUsedStoragePath());
        }
    }

    /**
     * Get domain used for cli session.
     * @return string
     * @throws \Salah3id\Domains\Exceptions\DomainNotFoundException
     */
    public function getUsedNow(): string
    {
        return $this->findOrFail($this->getFiles()->get($this->getUsedStoragePath()));
    }

    /**
     * Get laravel filesystem instance.
     *
     * @return Filesystem
     */
    public function getFiles(): Filesystem
    {
        return $this->files;
    }

    /**
     * Get domain assets path.
     *
     * @return string
     */
    public function getAssetsPath(): string
    {
        return $this->config('paths.assets');
    }

    /**
     * Get asset url from a specific domain.
     * @param string $asset
     * @return string
     * @throws InvalidAssetPath
     */
    public function asset($asset): string
    {
        if (Str::contains($asset, ':') === false) {
            throw InvalidAssetPath::missingDomainName($asset);
        }
        list($name, $url) = explode(':', $asset);

        $baseUrl = str_replace(public_path() . DIRECTORY_SEPARATOR, '', $this->getAssetsPath());

        $url = $this->url->asset($baseUrl . "/{$name}/" . $url);

        return str_replace(['http://', 'https://'], '//', $url);
    }

    /**
     * @inheritDoc
     */
    public function isEnabled(string $name): bool
    {
        return $this->findOrFail($name)->isEnabled();
    }

    /**
     * @inheritDoc
     */
    public function isDisabled(string $name): bool
    {
        return !$this->isEnabled($name);
    }

    /**
     * Enabling a specific domain.
     * @param string $name
     * @return void
     * @throws \Salah3id\Domains\Exceptions\DomainNotFoundException
     */
    public function enable($name)
    {
        $this->findOrFail($name)->enable();
    }

    /**
     * Disabling a specific domain.
     * @param string $name
     * @return void
     * @throws \Salah3id\Domains\Exceptions\DomainNotFoundException
     */
    public function disable($name)
    {
        $this->findOrFail($name)->disable();
    }

    /**
     * @inheritDoc
     */
    public function delete(string $name): bool
    {
        return $this->findOrFail($name)->delete();
    }

    /**
     * Update dependencies for the specified domain.
     *
     * @param string $domain
     */
    public function update($domain)
    {
        with(new Updater($this))->update($domain);
    }

    /**
     * Install the specified domain.
     *
     * @param string $name
     * @param string $version
     * @param string $type
     * @param bool   $subtree
     *
     * @return \Symfony\Component\Process\Process
     */
    public function install($name, $version = 'dev-master', $type = 'composer', $subtree = false)
    {
        $installer = new Installer($name, $version, $type, $subtree);

        return $installer->run();
    }

    /**
     * Get stub path.
     *
     * @return string|null
     */
    public function getStubPath()
    {
        if ($this->stubPath !== null) {
            return $this->stubPath;
        }

        if ($this->config('stubs.enabled') === true) {
            return $this->config('stubs.path');
        }

        return $this->stubPath;
    }

    /**
     * Set stub path.
     *
     * @param string $stubPath
     *
     * @return $this
     */
    public function setStubPath($stubPath)
    {
        $this->stubPath = $stubPath;

        return $this;
    }
}
