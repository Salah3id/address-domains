<?php

namespace Salah3id\Domains\Repository\Traits;

/**
 * Class TransformableTrait
 * @package Salah3id\Domains\Repository\Traits
 * @author Anderson Andrade <contato@andersonandra.de>
 */
trait TransformableTrait
{
    /**
     * @return array
     */
    public function transform()
    {
        return $this->toArray();
    }
}
