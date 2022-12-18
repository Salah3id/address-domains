<?php

namespace Salah3id\Domains\Laravel;

use Salah3id\Domains\FileRepository;

class LaravelFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createDomain(...$args)
    {
        return new Domain(...$args);
    }
}
