<?php

namespace Salah3id\Domains\Traits;

trait CanClearDomainsCache
{
    /**
     * Clear the domains cache if it is enabled
     */
    public function clearCache()
    {
        if (config('domains.cache.enabled') === true) {
            app('cache')->forget(config('domains.cache.key'));
        }
    }
}
