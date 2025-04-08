<?php

declare(strict_types=1);

namespace pvc\http\err;

use pvc\err\stock\LogicException;
use Throwable;

class InvalidConnectionTimeoutException extends LogicException
{
    public function __construct(int $badTimeout, ?Throwable $previous = null)
    {
        parent::__construct((string)$badTimeout, $previous);
    }
}
