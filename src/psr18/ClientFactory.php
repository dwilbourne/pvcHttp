<?php

namespace pvc\http\psr18;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Client\ClientInterface;

class ClientFactory
{
    public function createClient(): ClientInterface
    {
        return new GuzzleClient();
    }
}