<?php

namespace pvc\http\psr7;

use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

/**
 * standard implementations of Psr-7 compliant Uri objects all seem to suffer
 * the flaw that they are created with a string to parse and there is no parse
 * method in the spec.  So in order to properly inject a Uri, we need the
 * factory.
 */
class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): UriInterface
    {
        $guzzleUri = new \GuzzleHttp\Psr7\Uri($uri);
        return new Uri($guzzleUri);
    }
}