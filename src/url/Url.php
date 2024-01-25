<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace pvc\http\url;

use pvc\http\err\CurlInitException;
use pvc\http\err\InvalidPortNumberException;

/**
 * Class Url
 */
class Url
{
    /**
     * @var string
     */
    protected string $scheme;    // protocol e.g. http, https, ftp, etc.

    /**
     * @var string
     */
    protected string $host;

    /**
     * @var int
     */
    protected int $port;

    /**
     * @var string
     */
    protected string $user;

    /**
     * @var string
     */
    protected string $password;

    /**
     * @var string
     */
    protected string $path;

    /**
     * @var string
     *
     *  it is tempting to type this as \pvc\http\utl\QueryString, but because this class is really designed to be
     *  hydrated by the parse_url verb, it is typed as a string for the moment
     */
    protected string $query;

    /**
     * @var string
     */
    protected string $fragment;

    /**
     * @var int|null
     */
    protected int|null $httpStatusCode;

    /**
     * @var string
     */
    protected string $httpStatus;

    /**
     * @var string
     */
    protected string $curlErrorMessage;

    /**
     * @var array<int, string>
     */
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

    /**
     * @param array<string, string>|null $values
     */
    public function __construct(array $values = null)
    {
        if ($values) {
            $this->setAttributesFromArray($values);
        }
    }

    /**
     * setScheme
     * @param string $scheme
     */
    public function setScheme(string $scheme): void
    {
        $this->scheme = $scheme;
    }

    /**
     * getScheme
     * @return string|null
     */
    public function getScheme(): ?string
    {
        return $this->scheme ?? null;
    }

    /**
     * setHost
     * @param string $host
     */
    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * getHost
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host ?? null;
    }

    /**
     * setPort
     * @param int|string $port
     * @throws InvalidPortNumberException
     */
    public function setPort(int|string $port): void
    {
        if (!ctype_digit($port)) {
            throw new InvalidPortNumberException();
        }
        $this->port = (int)$port;
    }

    /**
     * getPort
     * @return int|null
     */
    public function getPort(): ?int
    {
        return $this->port ?? null;
    }

    /**
     * setUser
     * @param string $user
     */
    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    /**
     * getUser
     * @return string|null
     */
    public function getUser(): ?string
    {
        return $this->user ?? null;
    }

    /**
     * setPassword
     * @param string $pwd
     */
    public function setPassword(string $pwd): void
    {
        $this->password = $pwd;
    }

    /**
     * getPassword
     * @return string|null
     */
    public function getPassword(): ?string
    {
        return $this->password ?? null;
    }

    /**
     * setPath
     * @param string $path
     *
     * if setting it manually, remember that $path should not have the leading '/'.  parse_url removes it by default
     */
    public function setPath(string $path): void
    {
        $this->path = $path;
    }

    /**
     * getPath
     * @return string
     */
    public function getPath(): string
    {
        return $this->path ?? "";
    }

    /**
     * getPathAsArray
     * @return array<string>
     */
    public function getPathAsArray(): array
    {
        return explode('/', $this->getPath());
    }

    /**
     * setQuery
     * @param string $queryString
     */
    public function setQuery(string $queryString): void
    {
        $this->query = $queryString;
    }

    /**
     * getQuery
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * setFragment
     * @param string $fragment
     */
    public function setFragment(string $fragment): void
    {
        $this->fragment = $fragment;
    }

    /**
     * getFragment
     * @return string|null
     */
    public function getFragment(): ?string
    {
        return $this->fragment ?? null;
    }

    /**
     * getHttpStatusCode
     * @return int|null
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode ?? null;
    }

    /**
     * getHttpStatus
     * @return string|null
     */
    public function getHttpStatus(): ?string
    {
        return $this->httpStatus ?? '';
    }

    /**
     * getCurlErrorMessage
     * @return string|null
     */
    public function getCurlErrorMessage(): ?string
    {
        return $this->curlErrorMessage ?? '';
    }

    /**
     * setAttributesFromArray
     * @param array<string, string> $urlParts
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

    /**
     * exists
     * @return bool
     * @throws CurlInitException
     */
    public function exists(): bool
    {
        $this->httpStatusCode = null;
        $this->httpStatus = '';
        $this->curlErrorMessage = '';

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
            $this->curlErrorMessage = curl_error($ch);
            return false;
        }

        $this->httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->httpStatus = $this->httpStatusCodes[$this->httpStatusCode];
        @curl_close($ch);
        return ($this->httpStatusCode == 200);
    }
}
