<?php

namespace Salah3id\Domains\Contracts;

use Salah3id\Domains\Exceptions\DomainNotFoundException;
use Salah3id\Domains\Domain;

interface RepositoryInterface
{
    /**
     * Get all domains.
     *
     * @return mixed
     */
    public function all();

    /**
     * Get cached domains.
     *
     * @return array
     */
    public function getCached();

    /**
     * Scan & get all available domains.
     *
     * @return array
     */
    public function scan();

    /**
     * Get domains as domains collection instance.
     *
     * @return \Salah3id\Domains\Collection
     */
    public function toCollection();

    /**
     * Get scanned paths.
     *
     * @return array
     */
    public function getScanPaths();

    /**
     * Get list of enabled domains.
     *
     * @return mixed
     */
    public function allEnabled();

    /**
     * Get list of disabled domains.
     *
     * @return mixed
     */
    public function allDisabled();

    /**
     * Get count from all domains.
     *
     * @return int
     */
    public function count();

    /**
     * Get all ordered domains.
     * @param string $direction
     * @return mixed
     */
    public function getOrdered($direction = 'asc');

    /**
     * Get domains by the given status.
     *
     * @param int $status
     *
     * @return mixed
     */
    public function getByStatus($status);

    /**
     * Find a specific domain.
     *
     * @param $name
     * @return Domain|null
     */
    public function find(string $name);

    /**
     * Find a specific domain. If there return that, otherwise throw exception.
     *
     * @param $name
     *
     * @return mixed
     */
    public function findOrFail(string $name);

    public function getDomainPath($domainName);

    /**
     * @return \Illuminate\Filesystem\Filesystem
     */
    public function getFiles();

    /**
     * Get a specific config data from a configuration file.
     * @param string $key
     *
     * @param string|null $default
     * @return mixed
     */
    public function config(string $key, $default = null);

    /**
     * Get a domain path.
     *
     * @return string
     */
    public function getPath(): string;

    /**
     * Boot the domains.
     */
    public function boot(): void;

    /**
     * Register the domains.
     */
    public function register(): void;

    /**
     * Get asset path for a specific domain.
     *
     * @param string $domain
     * @return string
     */
    public function assetPath(string $domain): string;

    /**
     * Delete a specific domain.
     * @param string $domain
     * @return bool
     * @throws \Salah3id\Domains\Exceptions\DomainNotFoundException
     */
    public function delete(string $domain): bool;

    /**
     * Determine whether the given domain is activated.
     * @param string $name
     * @return bool
     * @throws DomainNotFoundException
     */
    public function isEnabled(string $name): bool;

    /**
     * Determine whether the given domain is not activated.
     * @param string $name
     * @return bool
     * @throws DomainNotFoundException
     */
    public function isDisabled(string $name): bool;
}
