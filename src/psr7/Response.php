<?php

namespace pvc\http\psr7;

use Psr\Http\Message\MessageInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;

class Response implements ResponseInterface
{
    protected ResponseInterface $response;

    public function __construct(GuzzleResponse $response)
    {
        $this->response = $response;
    }

    public function getProtocolVersion(): string
    {
        return $this->response->getProtocolVersion();
    }

    public function withProtocolVersion(string $version): MessageInterface
    {
        $this->response = $this->response->withProtocolVersion($version);
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function hasHeader(string $name): bool
    {
        return $this->response->hasHeader($name);
    }

    public function getHeader(string $name): array
    {
        return $this->response->getHeader($name);
    }

    public function getHeaderLine(string $name): string
    {
        return $this->response->getHeaderLine($name);
    }

    public function withHeader(string $name, $value): ResponseInterface
    {
        $this->response = $this->response->withHeader($name, $value);
        return $this;
    }

    public function withAddedHeader(string $name, $value): ResponseInterface
    {
        $this->response = $this->response->withAddedHeader($name, $value);
        return $this;
    }

    public function withoutHeader(string $name): ResponseInterface
    {
        $this->response = $this->response->withoutHeader($name);
        return $this;
    }

    public function getBody(): StreamInterface
    {
        return $this->response->getBody();
    }

    public function withBody(StreamInterface $body): ResponseInterface
    {
        $this->response = $this->response->withBody($body);
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function withStatus(
        int $code,
        string $reasonPhrase = ''
    ): ResponseInterface {
        $this->response = $this->response->withStatus($code, $reasonPhrase);
        return $this;
    }

    public function getReasonPhrase(): string
    {
        return $this->response->getReasonPhrase();
    }
}