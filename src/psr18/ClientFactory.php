<?php

namespace pvc\http\psr18;

use Psr\Http\Client\ClientInterface;

class ClientFactory
{
    public function createClient(): ClientInterface
    {
        return new \GuzzleHttp\Client();
    }
}