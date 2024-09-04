<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvc\http\mime;

use pvc\interfaces\http\mimetype\MimeTypeFactoryInterface;

/**
 * Class MimeTypeFactory
 */
class MimeTypeFactory implements MimeTypeFactoryInterface
{
    public function makeMimeType(): MimeType
    {
        return new MimeType();
    }
}