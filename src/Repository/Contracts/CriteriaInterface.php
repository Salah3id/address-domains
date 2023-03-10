<?php
namespace Salah3id\Domains\Repository\Contracts;

/**
 * Interface CriteriaInterface
 * @package Salah3id\Domains\Repository\Contracts
 * @author Anderson Andrade <contato@andersonandra.de>
 */
interface CriteriaInterface
{
    /**
     * Apply criteria in query repository
     *
     * @param                     $model
     * @param RepositoryInterface $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryInterface $repository);
}
