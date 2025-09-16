<?php

namespace pvc\http;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use pvc\http\err\ClientRuntimeException;
use pvc\http\err\InvalidConnectionTimeoutException;
use pvc\http\err\InvalidHttpVerbException;
use pvc\interfaces\http\UrlInterface;
use Throwable;

class HttpClient
{
    protected int $connectionTimeoutInSeconds = 3;

    /**
     * @var array<string>
     */
    protected array $httpVerbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS', 'CONNECT', 'TRACE'];

    public function __construct(protected GuzzleClient $guzzleClient)
    {
    }

    /**
     * @param int $connectionTimeoutInSeconds
     * @return void
     */
    public function setConnectionTimeout(int $connectionTimeoutInSeconds): void
    {
        if ($connectionTimeoutInSeconds < 1) {
            throw new InvalidConnectionTimeoutException($connectionTimeoutInSeconds);
        }
        $this->connectionTimeoutInSeconds = $connectionTimeoutInSeconds;
    }

    /**
     * @return int
     */
    public function getConnectionTimeout(): int
    {
        return $this->connectionTimeoutInSeconds;
    }

    /**
     * @param string $requestType
     * @param UrlInterface $url
     * @return ResponseInterface
     * @throws ClientRuntimeException
     */
    public function request(string $requestType, UrlInterface $url): ResponseInterface
    {
        $this->validateHttpVerb($requestType);
        $request = new Request($requestType, $url->render());
        $options = ['timeout' => $this->connectionTimeoutInSeconds];

        /**
         * the request type is valid and the url is properly formed at this point.  Any remaining problems are
         * some variety of runtime exception.....
         */
        try {
            return $this->guzzleClient->send($request, $options);
        } catch (Throwable $e) {
            throw new ClientRuntimeException($url->render(), $e);
        }
    }

    protected function validateHttpVerb(string $httpVerb): void
    {
        if (!in_array($httpVerb, $this->httpVerbs)) {
            throw new InvalidHttpVerbException($httpVerb);
        }
    }
}