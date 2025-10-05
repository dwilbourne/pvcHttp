<?php

namespace pvc\http\psr7;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

use GuzzleHttp\Psr7\Request as GuzzleRequest;
use pvc\http\err\InvalidHttpVerbException;

class Request implements RequestInterface
{
    /**
     * @var array<string>
     */
    protected array $httpVerbs
        = [
            'GET',
            'POST',
            'PUT',
            'PATCH',
            'DELETE',
            'HEAD',
            'OPTIONS',
            'CONNECT',
            'TRACE'
        ];

    protected RequestInterface $guzzleRequest;

    public function __construct(string $method, UriInterface $uri)
    {
        /**
         * guzzle allows customized (free form) verbs, pvc does not.
         */
        if (!in_array($method, $this->httpVerbs)) {
            throw new InvalidHttpVerbException($method);
        }

        $this->guzzleRequest = new GuzzleRequest($method, $uri->__toString());
    }

    public function getProtocolVersion(): string
    {
        return $this->guzzleRequest->getProtocolVersion();
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        $this->guzzleRequest = $this->guzzleRequest->withProtocolVersion(
            $version
        );
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->guzzleRequest->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->guzzleRequest->hasHeader($name);
    }

    public function getHeader(string $name): array
    {
        return $this->guzzleRequest->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->guzzleRequest->getHeaderLine($name);
    }

    public function withHeader(string $name, $value): RequestInterface
    {
        $this->guzzleRequest = $this->guzzleRequest->withHeader($name, $value);
        return $this;
    }

    public function withAddedHeader(string $name, $value): RequestInterface
    {
        $this->guzzleRequest = $this->guzzleRequest->withAddedHeader(
            $name,
            $value
        );
        return $this;
    }

    public function withoutHeader(string $name): RequestInterface
    {
        $this->guzzleRequest = $this->guzzleRequest->withoutHeader($name);
        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->guzzleRequest->getBody();
    }

    public function withBody(StreamInterface $body): RequestInterface
    {
        $this->guzzleRequest = $this->guzzleRequest->withBody($body);
        return $this;
    }

    public function getRequestTarget(): string
    {
        return $this->guzzleRequest->getRequestTarget();
    }

    public function withRequestTarget(string $requestTarget): RequestInterface
    {
        $this->guzzleRequest = $this->guzzleRequest->withRequestTarget(
            $requestTarget
        );
        return $this;
    }

    public function getMethod(): string
    {
        return $this->guzzleRequest->getMethod();
    }

    public function withMethod(string $method): RequestInterface
    {
        $this->guzzleRequest = $this->guzzleRequest->withMethod($method);
        return $this;
    }

    public function getUri(): UriInterface
    {
        return $this->guzzleRequest->getUri();
    }

    public function withUri(
        UriInterface $uri,
        bool $preserveHost = false
    ): RequestInterface {
        $this->guzzleRequest = $this->guzzleRequest->withUri(
            $uri,
            $preserveHost
        );
        return $this;
    }
}