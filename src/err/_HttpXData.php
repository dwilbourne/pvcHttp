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
            CurlInitException::class => 1000,
            InvalidPortNumberException::class => 1001,
            InvalidQuerystringParamNameException::class => 1002,
            InvalidQueryEncodingException::class => 1003,
            InvalidQuerystringException::class => 1004,
            MimeTypeCdnException::class => 1005,
            MimeTypesJsonDecodingException::class => 1006,
            InvalidMimeDetectionConstantException::class => 1007,
            ConflictingMimeTypesException::class => 1008,
            UnknownMimeTypeDetectedException::class => 1009,
        ];
    }

    public function getXMessageTemplates(): array
    {
        return [
            CurlInitException::class => 'curl_init call failed and returned false instead of a curl handle.',
            InvalidPortNumberException::class => 'invalid port number specified - must be an positive int or a string of digits',
            InvalidQuerystringParamNameException::class => 'Invalid querystring param name: must start with a letter and be only alphanumeric or underscore',
            InvalidQueryEncodingException::class => 'Invalid query encoding specified - see the php documentation for build_http_query',
            InvalidQuerystringException::class => 'Invalid querystring.',
            MimeTypeCdnException::class => 'runtime exception: cdn ${cdn} containing mime types in not currently available.',
            MimeTypesJsonDecodingException::class => 'Error decoding json string into MimeTypes object.',
            InvalidMimeDetectionConstantException::class => 'Invalid MimeType class constant used as parameter to setMimeTypeDetection',
            ConflictingMimeTypesException::class => 'Mime type detected from file ${filePath} and file extension do not agree.',
            UnknownMimeTypeDetectedException::class => 'Unknown mime type ${mimeType} detected from php function mime_content_type in file ${filePath}.',
        ];
    }
}