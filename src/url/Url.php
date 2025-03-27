<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace pvc\http\url;

use pvc\http\err\CurlInitException;
use pvc\http\err\InvalidPortNumberException;
use pvc\http\err\InvalidQuerystringException;
use pvc\interfaces\http\QueryStringInterface;
use pvc\interfaces\http\UrlInterface;

/**
 * Class Url
 *
 * This class is somewhat limited in scope because php has pretty good verbs for doing url-related tasks already.
 * parse_url can be used to test whether a string is syntactically viable as a url.
 */
class Url implements UrlInterface
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
     * @var string
     */
    protected string $port;

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

    protected QueryStringInterface $queryString;

    /**
     * @var string
     */
    protected string $fragment;

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
     * @var non-empty-string
     */
    protected string $qstrSeparator = '&';

    /**
     * @param int $code
     * @return string|null
     */
    public function getHttpStatusFromCode(int $code): ?string
    {
        return $this->httpStatusCodes[$code] ?? null;
    }

    /**
     * @param QueryStringInterface $queryString
     */
    public function __construct(QueryStringInterface $queryString)
    {
        $this->setQueryString($queryString);
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
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme ?? '';
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
     * @return string
     */
    public function getHost(): string
    {
        return $this->host ?? '';
    }

    /**
     * setPort
     * @param int|string $port
     * @throws InvalidPortNumberException
     */
    public function setPort(int|string $port): void
    {
        /**
         * ctype now requires a string, using an int is deprecated
         */
        if (!ctype_digit((string)$port)) {
            throw new InvalidPortNumberException();
        }
        $this->port = (string)$port;
    }

    /**
     * getPort
     * @return string
     */
    public function getPort(): string
    {
        return $this->port ?? '';
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
     * @return string
     */
    public function getUser(): string
    {
        return $this->user ?? '';
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
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password ?? '';
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
        return $this->path ?? '';
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
     * @param QueryStringInterface $queryString
     */
    public function setQueryString(QueryStringInterface $queryString): void
    {
        $this->queryString = $queryString;
    }

    /**
     * getQuery
     * @return QueryStringInterface
     */
    public function getQueryString(): QueryStringInterface
    {
        return $this->queryString;
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
     * @return string
     */
    public function getFragment(): string
    {
        return $this->fragment ?? '';
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
     * @throws InvalidQuerystringException
     */
    public function setAttributesFromArray(array $urlParts): void
    {
        foreach ($urlParts as $partName => $part) {
            switch ($partName) {
                case 'scheme':
                    $this->setScheme($part);
                    break;
                case 'host':
                    $this->setHost($part);
                    break;
                case 'port':
                    $this->setPort($part);
                    break;
                case 'user':
                    $this->setUser($part);
                    break;
                case 'password':
                    $this->setPassword($part);
                    break;
                case 'path':
                    $this->setPath($part);
                    break;
                case 'query':
                    foreach ($this->parseQueryString($part) as $name => $value) {
                        $this->queryString->addParam($name, $value);
                    }
                    break;
                case 'fragment':
                    $this->setFragment($part);
                    break;
            }
        }
    }

    /**
     * @param string $data
     * @return array<string, string>
     * @throws InvalidQuerystringException
     * replacement for parse_str that does not mangle parameter names
     */
    protected function parseQueryString(string $data): array
    {
        $params = [];
        $data = trim($data, '?');

        $paramStrings = explode($this->qstrSeparator, $data);

        foreach ($paramStrings as $paramString) {
            $array = explode('=', $paramString);

            /**
             * cannot have a string like 'a=1=2'.  Need 0 or 1 equals signs.  Zero equals signs is a parameter with no
             * value attached
             */
            if (count($array) > 2) {
                throw new InvalidQuerystringException();
            }

            $paramName = $array[0];
            $paramValue = $array[1] ?? '';

            /**
             * if the parameter name is duplicated in the querystring, this results in the last value being used
             */
            $params[$paramName] = $paramValue;
        }
        return $params;
    }


    /**
     * generateURLString
     * @param bool $encoded
     * @return string
     *
     * urlencode / urldecode translate the percent-encoded bits as well as plus signs.  rawurlencode
     * and rawurldecode do not translate plus signs, and are designed to be compliant with RFC 3986, which specifies
     * the syntaxes for URI's, URN's and URL's.
     */
    public function generateURLString(bool $encoded = true): string
    {
        $urlString = ($this->getScheme() ? $this->getScheme() . '://' : '');
        $urlString .= $this->getUser();

        /**
         * user is separated from password by a colon.  Does it make sense to output a password if there is no user?
         * For now, this outputs a password even if there is no user.
         */
        $urlString .= ($this->getPassword() ? ':' . $this->getPassword() : '');
        /**
         * separate userid / password from path with a '@'
         */
        if ($this->getUser() || $this->getPassword()) {
            $urlString .= '@';
        }

        $urlString .= $this->getHost();
        $urlString .= ($this->getPort() ? ':' . $this->getPort() : '');
        $urlString .= ($this->getPath() ? '/' . $this->getPath() : '');

        $query = $this->getQueryString()->render();
        $urlString .= ($query ? '?' . ($encoded ? urlencode($query) : $query) : '');
        $urlString .= ($this->getFragment() ? '#' . $this->getFragment() : '');

        return $urlString;
    }

    /**
     * exists
     * @return int
     * @throws CurlInitException
     * returns -1 if the curl_exec call fails, otherwise returns an http status code
     */
    public function sendRequest(): int
    {
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
            return -1;
        }
        @curl_close($ch);
        return curl_getinfo($ch, CURLINFO_HTTP_CODE);
    }
}
