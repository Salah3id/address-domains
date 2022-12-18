<?php

namespace Salah3id\Domains\Contracts;

use Salah3id\Domains\Domain;

interface ActivatorInterface
{
    /**
     * Enables a domain
     *
     * @param Domain $domain
     */
    public function enable(Domain $domain): void;

    /**
     * Disables a domain
     *
     * @param Domain $domain
     */
    public function disable(Domain $domain): void;

    /**
     * Determine whether the given status same with a domain status.
     *
     * @param Domain $domain
     * @param bool $status
     *
     * @return bool
     */
    public function hasStatus(Domain $domain, bool $status): bool;

    /**
     * Set active state for a domain.
     *
     * @param Domain $domain
     * @param bool $active
     */
    public function setActive(Domain $domain, bool $active): void;

    /**
     * Sets a domain status by its name
     *
     * @param  string $name
     * @param  bool $active
     */
    public function setActiveByName(string $name, bool $active): void;

    /**
     * Deletes a domain activation status
     *
     * @param  Domain $domain
     */
    public function delete(Domain $domain): void;

    /**
     * Deletes any domain activation statuses created by this class.
     */
    public function reset(): void;
}
