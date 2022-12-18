<?php

namespace Salah3id\Domains\Support\Config;

class GenerateConfigReader
{
    public static function read(string $value): GeneratorPath
    {
        return new GeneratorPath(config("domains.paths.generator.$value"));
    }
}
