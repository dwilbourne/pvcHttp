<?php

namespace pvc\http\psr18;

class ClientFactory
{
    public function createClient()
    {
        return new \GuzzleHttp\Client();
    }
}