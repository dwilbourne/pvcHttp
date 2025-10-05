<?php

namespace pvc\http\psr7;

use GuzzleHttp\Psr7\Uri as GuzzleUri;
use Psr\Http\Message\UriFactoryInterface;

/**
 * standard implementations of Psr-7 compliant Uri objects all seem to suffer
 * the flaw that they are created with a string to parse and there is no parse
 * method in the spec.  This is a byproduct of the design point that Uris should
 * be immutable.  So in order to properly inject a Uri, we need the
 * factory.
 */
class UriFactory implements UriFactoryInterface
{
    public function createUri(string $uri = ''): GuzzleUri
    {
        return new GuzzleUri($uri);
    }
}