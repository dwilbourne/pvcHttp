<?php

declare(strict_types=1);

namespace pvc\http\err;

use pvc\err\stock\LogicException;

class InvalidUrlException extends LogicException
{
    public function __construct(string $badUrl, ?\Throwable $previous = null)
    {
        parent::__construct($badUrl, $previous);
    }
}
