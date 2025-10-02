<?php

namespace pvc\http\psr7;

use Psr\Http\Message\UriInterface;
use GuzzleHttp\Psr7\Uri as GuzzleUri;

class Uri implements UriInterface
{
    public function __construct(protected GuzzleUri $uri)
    {
    }

    public function getScheme(): string
    {
        return $this->uri->getScheme();
    }

    public function getAuthority(): string
    {
        return $this->uri->getAuthority();
    }

    public function getUserInfo(): string
    {
        return $this->uri->getUserInfo();
    }

    public function getHost(): string
    {
        return $this->uri->getHost();
    }

    public function getPort(): ?int
    {
        return $this->uri->getPort();
    }

    public function getPath(): string
    {
        return $this->uri->getPath();
    }

    public function getQuery(): string
    {
        return $this->uri->getQuery();
    }

    public function getFragment(): string
    {
        return $this->uri->getFragment();
    }

    public function withScheme(string $scheme): UriInterface
    {
        return $this->uri->withScheme($scheme);
    }

    public function withUserInfo(
        string $user,
        ?string $password = null
    ): UriInterface {
        return $this->uri->withUserInfo($user, $password);
    }

    public function withHost(string $host): UriInterface
    {
        return $this->uri->withHost($host);
    }

    public function withPort(?int $port): UriInterface
    {
        return $this->uri->withPort($port);
    }

    public function withPath(string $path): UriInterface
    {
        return $this->uri->withPath($path);
    }

    public function withQuery(string $query): UriInterface
    {
        return $this->uri->withQuery($query);
    }

    public function withFragment(string $fragment): UriInterface
    {
        return $this->uri->withFragment($fragment);
    }

    public function __toString(): string
    {
        return $this->uri->__toString();
    }
}