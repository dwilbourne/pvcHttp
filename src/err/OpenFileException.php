<?php

namespace pvc\http\err;

use pvc\err\stock\RuntimeException;
use Throwable;

class OpenFileException extends RuntimeException
{
    public function __construct(
        string $fileName,
        string $mode,
        ?Throwable $previous = null
    ) {
        parent::__construct($fileName, $mode, $previous);
    }
}