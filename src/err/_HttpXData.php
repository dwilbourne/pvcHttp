<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @noinspection PhpCSValidationInspection
 */
declare(strict_types=1);

namespace pvc\http\err;

use pvc\err\XDataAbstract;

/**
 * Class _ParserXData
 */
class _HttpXData extends XDataAbstract
{

    public function getLocalXCodes(): array
    {
        return [
            MimeTypeCdnException::class => 1005,
            MimeTypesJsonDecodingException::class => 1006,
            MimeTypesUnreadableStreamException::class => 1007,
            UnknownMimeTypeDetectedException::class => 1009,
            InvalidHttpVerbException::class => 1017,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            MimeTypeCdnException::class => 'runtime exception: cdn ${cdn} containing mime types in not currently available.',
            MimeTypesJsonDecodingException::class => 'Error decoding json string into MimeTypes object.',
            MimeTypesUnreadableStreamException::class => 'Unable to read stream or unable to detect mime type of sdtream.',
            UnknownMimeTypeDetectedException::class => 'Unknown mime type detected from php function mime_content_type in stream.',
            InvalidHttpVerbException::class => 'Invalid HTTP verb ${badHttpVerb} provided.',
        ];
    }
}