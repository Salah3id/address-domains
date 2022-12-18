<?php
namespace Salah3id\Domains\Repository\Events;

/**
 * Class RepositoryEntityCreated
 * @package Salah3id\Domains\Repository\Events
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "created";
}
