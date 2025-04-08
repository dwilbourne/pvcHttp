<?php

declare(strict_types=1);

namespace pvc\http\err;

use pvc\err\stock\RuntimeException;
use Throwable;

class ClientRuntimeException extends RuntimeException
{
    public function __construct(string $url, ?Throwable $previous = null)
    {
        parent::__construct($url, $previous);
    }
}
