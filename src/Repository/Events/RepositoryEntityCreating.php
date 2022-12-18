<?php

namespace Salah3id\Domains\Repository\Events;

use Illuminate\Database\Eloquent\Model;
use Salah3id\Domains\Repository\Contracts\RepositoryInterface;

/**
 * Class RepositoryEntityCreated
 *
 * @package Salah3id\Domains\Repository\Events
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryEntityCreating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "creating";

    public function __construct(RepositoryInterface $repository, array $model)
    {
        parent::__construct($repository);
        $this->model = $model;
    }
}
