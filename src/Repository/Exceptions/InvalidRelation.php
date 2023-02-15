<?php

namespace Salah3id\Domains\Repository\Exceptions;

class InvalidRelation extends \Exception
{
    public $message = 'Order by relation allows only following relations : BelongsTo, HasOne and HasMany.';
}