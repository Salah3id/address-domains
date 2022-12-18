<?php

namespace Salah3id\Domains;

use Illuminate\Cache\CacheManager;
use Illuminate\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;
use Illuminate\Translation\Translator;
use Salah3id\Domains\Contracts\ActivatorInterface;

abstract class Domain
{
    use Macroable;

    /**
     * The laravel|lumen application instance.
     *
     * @var \Illuminate\Contracts\Foundation\Application|\Laravel\Lumen\Application
     */
    protected $app;

    /**
     * The domain name.
     *
     * @var
     */
    protected $name;

    /**
     * The domain path.
     *
     * @var string
     */
    protected $path;

    /**
     * @var array of cached Json objects, keyed by filename
     */
    protected $domainJson = [];
    /**
     * @var CacheManager
     */
    private $cache;
    /**
     * @var Filesystem
     */
    private $files;
    /**
     * @var Translator
     */
    private $translator;
    /**
     * @var ActivatorInterface
     */
    private $activator;

    /**
     * The constructor.
     * @param Container $app
     * @param $name
     * @param $path
     */
    public function __construct(Container $app, string $name, $path)
    {
        $this->name = $name;
        $this->path = $path;
        $this->cache = $app['cache'];
        $this->files = $app['files'];
        $this->translator = $app['translator'];
        $this->activator = $app[ActivatorInterface::class];
        $this->app = $app;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get name in lower case.
     *
     * @return string
     */
    public function getLowerName(): string
    {
        return strtolower($this->name);
    }

    /**
     * Get name in studly case.
     *
     * @return string
     */
    public function getStudlyName(): string
    {
        return Str::studly($this->name);
    }

    /**
     * Get name in snake case.
     *
     * @return string
     */
    public function getSnakeName(): string
    {
        return Str::snake($this->name);
    }

    /**
     * Get description.
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->get('description');
    }

    /**
     * Get priority.
     *
     * @return string
     */
    public function getPriority(): string
    {
        return $this->get('priority');
    }

    /**
     * Get path.
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set path.
     *
     * @param string $path
     *
     * @return $this
     */
    public function setPath($path): Domain
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Bootstrap the application events.
     */
    public function boot(): void
    {
        if (config('domains.register.translations', true) === true) {
            $this->registerTranslation();
        }

        if ($this->isLoadFilesOnBoot()) {
            $this->registerFiles();
        }

        $this->fireEvent('boot');
    }

    /**
     * Register domain's translation.
     *
     * @return void
     */
    protected function registerTranslation(): void
    {
        $lowerName = $this->getLowerName();

        $langPath = $this->getPath() . '/Resources/lang';

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, $lowerName);
        }
    }

    /**
     * Get json contents from the cache, setting as needed.
     *
     * @param string $file
     *
     * @return Json
     */
    public function json($file = null): Json
    {
        if ($file === null) {
            $file = 'domain.json';
        }

        return Arr::get($this->domainJson, $file, function () use ($file) {
            return $this->domainJson[$file] = new Json($this->getPath() . '/' . $file, $this->files);
        });
    }

    /**
     * Get a specific data from json file by given the key.
     *
     * @param string $key
     * @param null $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->json()->get($key, $default);
    }

    /**
     * Get a specific data from composer.json file by given the key.
     *
     * @param $key
     * @param null $default
     *
     * @return mixed
     */
    public function getComposerAttr($key, $default = null)
    {
        return $this->json('composer.json')->get($key, $default);
    }

    /**
     * Register the domain.
     */
    public function register(): void
    {
        $this->registerAliases();

        $this->registerProviders();

        if ($this->isLoadFilesOnBoot() === false) {
            $this->registerFiles();
        }

        $this->fireEvent('register');
    }

    /**
     * Register the domain event.
     *
     * @param string $event
     */
    protected function fireEvent($event): void
    {
        $this->app['events']->dispatch(sprintf('domains.%s.' . $event, $this->getLowerName()), [$this]);
    }
    /**
     * Register the aliases from this domain.
     */
    abstract public function registerAliases(): void;

    /**
     * Register the service providers from this domain.
     */
    abstract public function registerProviders(): void;

    /**
     * Get the path to the cached *_domain.php file.
     *
     * @return string
     */
    abstract public function getCachedServicesPath(): string;

    /**
     * Register the files from this domain.
     */
    protected function registerFiles(): void
    {
        foreach ($this->get('files', []) as $file) {
            include $this->path . '/' . $file;
        }
    }

    /**
     * Handle call __toString.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getStudlyName();
    }

    /**
     * Determine whether the given status same with the current domain status.
     *
     * @param bool $status
     *
     * @return bool
     */
    public function isStatus(bool $status): bool
    {
        return $this->activator->hasStatus($this, $status);
    }

    /**
     * Determine whether the current domain activated.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->activator->hasStatus($this, true);
    }

    /**
     *  Determine whether the current domain not disabled.
     *
     * @return bool
     */
    public function isDisabled(): bool
    {
        return !$this->isEnabled();
    }

    /**
     * Set active state for current domain.
     *
     * @param bool $active
     *
     * @return void
     */
    public function setActive(bool $active): void
    {
        $this->activator->setActive($this, $active);
    }

    /**
     * Disable the current domain.
     */
    public function disable(): void
    {
        $this->fireEvent('disabling');

        $this->activator->disable($this);
        $this->flushCache();

        $this->fireEvent('disabled');
    }

    /**
     * Enable the current domain.
     */
    public function enable(): void
    {
        $this->fireEvent('enabling');

        $this->activator->enable($this);
        $this->flushCache();

        $this->fireEvent('enabled');
    }

    /**
     * Delete the current domain.
     *
     * @return bool
     */
    public function delete(): bool
    {
        $this->activator->delete($this);

        return $this->json()->getFilesystem()->deleteDirectory($this->getPath());
    }

    /**
     * Get extra path.
     *
     * @param string $path
     *
     * @return string
     */
    public function getExtraPath(string $path): string
    {
        return $this->getPath() . '/' . $path;
    }

    /**
     * Check if can load files of domain on boot method.
     *
     * @return bool
     */
    protected function isLoadFilesOnBoot(): bool
    {
        return config('domains.register.files', 'register') === 'boot' &&
            // force register method if option == boot && app is AsgardCms
            !class_exists('\Domains\Core\Foundation\AsgardCms');
    }

    private function flushCache(): void
    {
        if (config('domains.cache.enabled')) {
            $this->cache->store(config('domains.cache.driver'))->flush();
        }
    }

    /**
     * Register a translation file namespace.
     *
     * @param  string  $path
     * @param  string  $namespace
     * @return void
     */
    private function loadTranslationsFrom(string $path, string $namespace): void
    {
        $this->translator->addNamespace($namespace, $path);
    }
}
