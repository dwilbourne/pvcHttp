<?php

declare(strict_types=1);

namespace pvc\http\err;

use pvc\err\stock\Exception;
use Throwable;

class UrlMustBeReadableException extends Exception
{
    public function __construct(string $url, ?Throwable $previous = null)
    {
        parent::__construct($url, $previous);
    }
}