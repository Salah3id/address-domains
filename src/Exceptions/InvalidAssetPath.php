<?php

namespace Salah3id\Domains\Exceptions;

class InvalidAssetPath extends \Exception
{
    public static function missingDomainName($asset)
    {
        return new static("Domain name was not specified in asset [$asset].");
    }
}
