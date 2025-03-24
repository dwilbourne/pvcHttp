<?php

namespace pvc\http\err;

use pvc\err\stock\RuntimeException;
use Throwable;

class UnknownMimeTypeDetectedException extends RuntimeException
{
    public function __construct(string $mimeType, string $filePath, ?Throwable $previous = null)
    {
        parent::__construct($mimeType, $filePath, $previous);
    }
}