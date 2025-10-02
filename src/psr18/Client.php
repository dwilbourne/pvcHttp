<?php

namespace pvc\http\psr18;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class Client implements ClientInterface
{
    /**
     * @param  GuzzleClient  $guzzleClient
     */
    public function __construct(protected GuzzleClient $guzzleClient)
    {
    }

    /**
     * sendRequest
     *
     * @param  RequestInterface  $request
     *
     * @return ResponseInterface
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return $this->guzzleClient->send($request);
    }
}