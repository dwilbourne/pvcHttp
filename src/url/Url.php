<?php

/**
 * @author Doug Wilbourne (dougwilbourne@gmail.com)
 */

namespace pvc\http\url;

use pvc\http\err\InvalidUrlException;
use pvc\interfaces\http\QueryStringInterface;
use pvc\interfaces\http\UrlInterface;
use pvc\interfaces\parser\ParserQueryStringInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class Url
 *
 * The purpose of the class is to make it easy to manipulate the various parts of a url without having to resort
 * to string manipulation.
 *
 * There is no validation done when setting the values of the individual components.  But, by default the render
 * method will validate the url before returning the generated url and will throw an exception if it is not valid.
 * This behavior is configurable.
 *
 * You can create a url from scratch with this object.  You can also start with an existing url and hydrate this
 * object using the ParserUrl class found in the pvc Parser library.  And you can even hydrate this object
 * directly from an array which is produced by php's parse_url method.  Just be aware that the parse_url verb
 * will mangle pieces of a url when it finds characters it does not like.  The ParserUrl class validates a url
 * before parsing and automatically hydrates the Url object for you.
 *
 * @phpstan-type UrlShape array{string: 'scheme', string: 'host', non-negative-int: 'port', string: 'user', string: 'password', string: 'path', string: 'fragment'}
 */
class Url implements UrlInterface
{
    /**
     * @var string
     * protocol e.g. http, https, ftp, etc.
     */
    public string $scheme = '';

    /**
     * @var string
     */
    public string $host = '';

    /**
     * @var non-negative-int|null
     */
    public int|null $port = null;

    /**
     * @var string
     */
    public string $user = '';

    /**
     * @var string
     */
    public string $password = '';

    /**
     * @var string
     */
    public string $path = '';

    /**
     * @var string
     */
    public string $fragment = '';

    /**
     * @param ParserQueryStringInterface $parserQueryString
     * @param ValTesterInterface<string> $urlTester
     */
    public function __construct(
        protected ParserQueryStringInterface $parserQueryString,
        protected ValTesterInterface         $urlTester,
    )
    {
    }

    /**
     * getQueryString
     * @return QueryStringInterface
     */
    public function getQueryString(): QueryStringInterface
    {
        /** @var QueryStringInterface $qstr */
        $qstr = $this->parserQueryString->getParsedValue();
        return $qstr;
    }

    /**
     * @param UrlShape $urlParts
     * @return void
     */
    public function hydrateFromArray(array $urlParts): void
    {
        foreach ($urlParts as $partName => $part) {
            switch ($partName) {
                case 'scheme':
                    $this->scheme = $part;
                    break;
                case 'host':
                    $this->host = $part;
                    break;
                case 'port':
                    /**
                     * get a very odd phpstan error here: $port (int<0, max>|null) does not accept 'fragment'|'port'
                     * @phpstan-ignore-next-line
                     */
                    $this->port = $part;
                    break;
                case 'user':
                    $this->user = $part;
                    break;
                case 'password':
                    $this->password = $part;
                    break;
                case 'path':
                    $this->path = $part;
                    break;
                case 'query':
                    $this->parserQueryString->parse($part);
                    /**
                     * nothing needs to be set.  The querystring parser contains a querystring object.  You can get
                     * that object and manipulate it if you need to.
                     */
                    break;
                case 'fragment':
                    $this->fragment = $part;
                    break;
            }
        }
    }

    /**
     * generateURLString
     * @param bool $validateBeforeRender
     * @return string
     * @throws InvalidUrlException
     *
     */
    public function render(bool $validateBeforeRender = true): string
    {
        $urlString = '';
        $urlString .= $this->scheme ? $this->scheme . '://' : '';
        $urlString .= $this->user;

        /**
         * user is separated from password by a colon.  Does it make sense to output a password if there is no user?
         * For now, this outputs a password even if there is no user.
         */
        $urlString .= $this->password ? ':' . $this->password : '';

        /**
         * separate userid / password from path with a '@'
         */
        $urlString .= ($this->user || $this->password) ? '@' : '';

        $urlString .= $this->host;
        $urlString .= $this->port ? ':' . $this->port : '';
        $urlString .= $this->path;

        $query = $this->getQueryString()->render();
        $urlString .= $query ? '?' . $query : '';

        $urlString .= $this->fragment ? '#' . $this->fragment : '';

        if ($validateBeforeRender && !$this->urlTester->testValue($urlString)) {
            throw new InvalidUrlException($urlString);
        }

        return $urlString;
    }
}
