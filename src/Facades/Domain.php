<?php

namespace Salah3id\Domains\Facades;

use Illuminate\Support\Facades\Facade;

class Domain extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'domains';
    }
}
