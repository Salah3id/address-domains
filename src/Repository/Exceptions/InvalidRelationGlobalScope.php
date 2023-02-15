<?php

namespace Salah3id\Domains\Repository\Exceptions;

class InvalidRelationGlobalScope extends \Exception
{
    public $message = 'Order by relation allows only SoftDeletingScope global scope.';
}