<?php
namespace Salah3id\Domains\Repository\Events;

/**
 * Class RepositoryEntityDeleted
 * @package Salah3id\Domains\Repository\Events
 * @author Anderson Andrade <contato@andersonandra.de>
 */
class RepositoryEntityDeleting extends RepositoryEventBase
{
    /**
     * @var string
     */
    protected $action = "deleting";
}
