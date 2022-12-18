<?php
namespace Salah3id\Domains\Repository\Contracts;

/**
 * Interface Transformable
 * @package Salah3id\Domains\Repository\Contracts
 * @author Anderson Andrade <contato@andersonandra.de>
 */
interface Transformable
{
    /**
     * @return array
     */
    public function transform();
}
