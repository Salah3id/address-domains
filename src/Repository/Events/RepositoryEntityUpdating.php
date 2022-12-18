<?php
namespace Salah3id\Domains\Repository\Events;

/**
 * Class RepositoryEntityUpdated
 * @package Salah3id\Domains\Repository\Events
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryEntityUpdating extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "updating";
}
