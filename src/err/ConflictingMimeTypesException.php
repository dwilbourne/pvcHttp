<?php

namespace pvc\http\err;

use pvc\err\stock\RuntimeException;
use Throwable;

class ConflictingMimeTypesException extends RuntimeException
{
    public function __construct(string $filePath, ?Throwable $previous = null)
    {
        parent::__construct($filePath, $previous);
    }
}