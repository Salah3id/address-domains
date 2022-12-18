<?php

namespace Salah3id\Domains\Lumen;

use Salah3id\Domains\FileRepository;

class LumenFileRepository extends FileRepository
{
    /**
     * {@inheritdoc}
     */
    protected function createDomain(...$args)
    {
        return new Domain(...$args);
    }
}
