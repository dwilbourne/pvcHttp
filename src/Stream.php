<?php

namespace pvc\http;

use pvc\http\err\InvalidResourceException;
use pvc\http\err\InvalidStreamHandleException;
use pvc\http\err\MimeTypesUnreadableStreamException;
use pvc\interfaces\http\UrlInterface;

class Stream
{
    /**
     * @param UrlInterface $url
     * @return resource
     * @throws MimeTypesUnreadableStreamException
     */
    public static function openForReading(UrlInterface $url)
    {
        $urlString = $url->render();

        if (!$handle = fopen($urlString, 'r')) {
            throw new MimeTypesUnreadableStreamException($urlString);
        }
        return $handle;
    }

    /**
     * @param resource $handle
     * @return void
     * @throws InvalidResourceException
     * @throws InvalidStreamHandleException
     */
    public static function close($handle): void
    {
        if (!is_resource($handle)) {
            throw new InvalidResourceException();
        }
        if (get_resource_type($handle) !== 'stream') {
            throw new InvalidStreamHandleException();
        }
        fclose($handle);
    }
}