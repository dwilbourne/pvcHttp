<?php

namespace pvc\url;

use pvc\msg\MsgRetrievalInterface;
use pvc\url\err\CurlInitException;

class Url
{
    protected string $scheme;    // protocol e.g. http, https, ftp, etc.
    protected string $host;
    protected int $port;
    protected string $user;
    protected string $password;
    protected string $path;
    protected string $query;
    protected string $fragment;

    protected ? MsgRetrievalInterface $errmsg;

    protected int $httpStatusCode;
    protected string $httpStatus;
    protected string $curlErrorMessage;

    private $httpStatusCodes = array(
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Unordered Collection',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        450 => 'Blocked by Windows Parental Controls',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error'
    );

    public function __construct(array $values = null)
    {
        if ($values) {
            $this->setAttributesFromArray($values);
        }
    }

    public function setScheme(string $scheme)
    {
        $this->scheme = $scheme;
    }

    public function getScheme(): ?string
    {
        return $this->scheme ?? null;
    }

    public function setHost(string $host)
    {
        $this->host = $host;
    }

    public function getHost(): ?string
    {
        return $this->host ?? null;
    }

    public function setPort(int $port)
    {
        $this->port = $port;
    }

    public function getPort(): ?int
    {
        return $this->port ?? null;
    }

    public function setUser(string $user)
    {
        $this->user = $user;
    }

    public function getUser(): ?string
    {
        return $this->user ?? null;
    }

    public function setPassword(string $pwd)
    {
        $this->password = $pwd;
    }

    public function getPassword(): ?string
    {
        return $this->password ?? null;
    }

    // if setting it manually, remember that $path should not have the leading '/'.  parse_url removes it
    // by default
    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath() :? string
    {
        return $this->path;
    }

    public function getPathAsArray(): array
    {
        return explode('/', $this->getPath());
    }

    public function setQuery(string $queryString)
    {
        $this->query = $queryString;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function setFragment(string $fragment)
    {
        $this->fragment = $fragment;
    }

    public function getFragment(): ?string
    {
        return $this->fragment ?? null;
    }

    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    public function getHttpStatus(): ?string
    {
        return $this->httpStatus;
    }

    public function getCurlErrorMessage(): ?string
    {
        return $this->curlErrorMessage;
    }

    /**
     * setAttributesFromArray
     * @param array $urlParts
     * the indices for the array should be the same ones produced by the php verb parse_url
     */
    public function setAttributesFromArray(array $urlParts) : void
    {
        foreach ($urlParts as $partName => $part) {
            switch ($partName) {
                case "scheme":
                    $this->setScheme($part);
                    break;
                case "host":
                    $this->setHost($part);
                    break;
                case "port":
                    $this->setPort($part);
                    break;
                case "user":
                    $this->setUser($part);
                    break;
                case "password":
                    $this->setPassword($part);
                    break;
                case "path":
                    $this->setPath($part);
                    break;
                case "query":
                    $this->setQuery($part);
                    break;
                case "fragment":
                    $this->setFragment($part);
                    break;
            }
        }
    }

    public function getErrmsg(): ?MsgRetrievalInterface
    {
        return $this->errmsg;
    }

    /**
     * generateURLString
     * @return string
     *
     * urlencode / urldecode translate the percent-encoded bits as well as plus signs.  rawurlencode
     * and rawurldecode do not translate plus signs, and are designed to be compliant with RFC 3986, which specifies
     * the syntaxes for URI's, URN's and URL's.
     */
    public function generateURLString(): string
    {
        $scheme = !empty($this->scheme) ? $this->scheme . '://' : '';
        $host = !empty($this->host) ? $this->host : '';
        $port = !empty($this->port) ? ':' . $this->port : '';
        $user = !empty($this->user) ? $this->user : '';
        $pass = !empty($this->password) ? ':' . $this->password : '';
        $pass = ($user || $pass) ? "$pass@" : '';
        $path = !empty($this->path) ? "/" . $this->path : '';
        $query = !empty($this->query) ? '?' . $this->query : '';
        $fragment = !empty($this->fragment) ? '#' . $this->fragment : '';
        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    public function exists(): bool
    {
       $ch = curl_init($this->generateURLString());
        if ($ch === false) {
            throw new CurlInitException();
        }

        /*
         * these will never fail/error as long as $ch is valid but go ahead and put in error suppression
         */
        @curl_setopt($ch, CURLOPT_HEADER, true);
        @curl_setopt($ch, CURLOPT_NOBODY, true);
        @curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        /*
         * curl_exec returns true on success, false on failure, or some sort of string if CURLOPT_RETURNTRANSFER
         * is set (which it is not).
         */
        if (false === curl_exec($ch)) {
            return false;
        }

        $this->httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->httpStatus = $this->httpStatusCodes[$this->httpStatusCode];
        $this->curlErrorMessage = curl_error($ch);
        @curl_close($ch);
        return ($this->httpStatusCode == 200);
    }
}
