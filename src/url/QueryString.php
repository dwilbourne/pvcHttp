<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\http\url;

use pvc\http\err\InvalidQueryEncodingException;
use pvc\http\err\InvalidQuerystringParamNameException;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Encapsulate the idea of a querystring.  Feed the params attribute to http_build_query to get an encoded querystring.
 *
 */
class QueryString
{
    /**
     * @var array<string, string>
     */
    protected array $params = [];

    /**
     * @var int
     * this is the default for http_build_query
     */
    protected int $queryEncoding = PHP_QUERY_RFC1738;

    /**
     * @var ValTesterInterface<string>
     */
    protected ValTesterInterface $querystringParamNameTester;

    /**
     * @param ValTesterInterface<string> $querystringParamNameTester
     */
    public function __construct(ValTesterInterface $querystringParamNameTester)
    {
        $this->querystringParamNameTester = $querystringParamNameTester;
    }

    /**
     * setParams
     * @param array<string, string> $params
     * @throws InvalidQuerystringParamNameException
     */
    public function setParams(array $params) : void
    {
        foreach ($params as $key => $value) {
            $this->addParam($key, $value);
        }
    }

    /**
     * addParam
     * @param string $varName
     * @param string $value
     * @throws InvalidQuerystringParamNameException
     */
    public function addParam(string $varName, string $value): void
    {
        if (!$this->querystringParamNameTester->testValue($varName)) {
            throw new InvalidQuerystringParamNameException();
        }
        $this->params[$varName] = $value;
    }

    /**
     * getParams
     * @return array<string, string>
     */
    public function getParams() : array
    {
        return $this->params;
    }

    /**
     * setQueryEncoding
     * @param int $encoding
     * @throws InvalidQueryEncodingException
     */
    public function setQueryEncoding(int $encoding): void
    {
        if (!in_array($encoding, [PHP_QUERY_RFC1738, PHP_QUERY_RFC3986])) {
            throw new InvalidQueryEncodingException();
        }
        $this->queryEncoding = $encoding;
    }

    /**
     * getQueryEncoding
     * @return int
     */
    public function getQueryEncoding(): int
    {
        return $this->queryEncoding;
    }

    /**
     * render
     * @return string
     *
     * the numeric prefix parameter will never be used because the querystringParameterTester ensures the parameter
     * name starts with a letter.
     *
     * although http_build_query provides the ability to make the argument separator something else, it's hard to see
     * why anyone would really want to do so.  The default is the W3 standard (recommendation). which is '&'
     *
     * The method does not prepend the querystring with a '?'.  The '?' is a delimiter in the URL, not really part of
     * the querystring per se.  The '?' is inserted when the URL is rendered.
     */
    public function render(): string
    {
        $numericPrefix = '';
        $argSeparator = null;
        return http_build_query($this->getParams(), $numericPrefix, $argSeparator, $this->queryEncoding);
    }
}
