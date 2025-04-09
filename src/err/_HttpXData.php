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
            MimeTypesUnreadableStreamException::class => 1007,
            UnknownMimeTypeDetectedException::class => 1009,
            ClientRuntimeException::class => 1010,
            InvalidUrlException::class => 1012,
            DetectMimeTypeResourceException::class => 1014,
            InvalidResourceException::class => 1015,
            InvalidStreamHandleException::class => 1016,
            InvalidHttpVerbException::class => 1017,
            InvalidConnectionTimeoutException::class => 1018,
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
            MimeTypesUnreadableStreamException::class => 'Unable to read stream or unable to detect mime type of sdtream.',
            UnknownMimeTypeDetectedException::class => 'Unknown mime type detected from php function mime_content_type in stream.',
            InvalidUrlException::class => 'Invalid url ${badUrl} could not be parsed.',
            ClientRuntimeException::class => 'Unable to connect to ${url}.',
            InvalidConnectionTimeoutException::class => 'Invalid connection timeout ${badTimeout} - must be > 0.',
            DetectMimeTypeResourceException::class => 'First arrgument to detect method must be either a filename or a resource (e.g. stream)',
            InvalidResourceException::class => 'Invalid resource: either is not a resource or resource has already been closed.',
            InvalidStreamHandleException::class => 'Resource is not a handle to a stream resource.',
            InvalidHttpVerbException::class => 'Invalid HTTP verb ${badHttpVerb} provided.',
        ];
    }
}