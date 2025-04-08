<?php

namespace pvc\http\err;

use pvc\err\stock\LogicException;

class InvalidHttpVerbException extends LogicException
{
    public function __construct(string $badHttpVerb, ?\Throwable $previous = null)
    {
        parent::__construct($badHttpVerb, $previous);
    }
}