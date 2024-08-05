<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\http\err;

use pvc\err\stock\RuntimeException;
use Throwable;

/**
 * Class MimeTypeCdnException
 */
class MimeTypeCdnException extends RuntimeException
{
    public function __construct(string $cdn, Throwable $prev = null)
    {
        parent::__construct($cdn, $prev);
    }
}