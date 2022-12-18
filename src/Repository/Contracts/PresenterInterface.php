<?php
namespace Salah3id\Domains\Repository\Contracts;

/**
 * Interface PresenterInterface
 * @package Salah3id\Domains\Repository\Contracts
 * @author Anderson Andrade <contato@andersonandra.de>
 */
interface PresenterInterface
{
    /**
     * Prepare data to present
     *
     * @param $data
     *
     * @return mixed
     */
    public function present($data);
}
