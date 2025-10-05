<?php

namespace pvc\http\psr7;

use Psr\Http\Message\UriInterface;
use GuzzleHttp\Psr7\Uri as GuzzleUri;

class Uri implements UriInterface
{
    protected UriInterface $uri;

    public function __construct(GuzzleUri $uri)
    {
        $this->uri = $uri;
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
        $this->uri = $this->uri->withScheme($scheme);
        return $this;
    }

    public function withUserInfo(
        string $user,
        ?string $password = null
    ): UriInterface {
        $this->uri = $this->uri->withUserInfo($user, $password);
        return $this;
    }

    public function withHost(string $host): UriInterface
    {
        $this->uri = $this->uri->withHost($host);
        return $this;
    }

    public function withPort(?int $port): UriInterface
    {
        $this->uri = $this->uri->withPort($port);
        return $this;
    }

    public function withPath(string $path): UriInterface
    {
        $this->uri = $this->uri->withPath($path);
        return $this;
    }

    public function withQuery(string $query): UriInterface
    {
        $this->uri = $this->uri->withQuery($query);
        return $this;
    }

    public function withFragment(string $fragment): UriInterface
    {
        $this->uri = $this->uri->withFragment($fragment);
        return $this;
    }

    public function __toString(): string
    {
        return $this->uri->__toString();
    }
}