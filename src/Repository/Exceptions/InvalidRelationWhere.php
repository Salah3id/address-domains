<?php

namespace Salah3id\Domains\Repository\Exceptions;

class InvalidRelationWhere extends \Exception
{
    public $message = 'Order by relation allows only following where(orWhere) clauses type on relation : ->where($column, $operator, $value) and ->where([$column => $value]).';
}