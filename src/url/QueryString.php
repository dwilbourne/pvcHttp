<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace pvc\http\url;

use pvc\http\err\InvalidQueryEncodingException;
use pvc\http\err\InvalidQuerystringException;
use pvc\http\err\InvalidQuerystringParamNameException;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * Class QueryString
 *
 * Encapsulate the idea of a querystring.
 */
class QueryString
{
    /**
     * @var array<string, string>
     *
     * param name => value pairs.  Because http_build_query's first argument is an array (or an object), and because
     * it uses the keys to the array to generate parameter names, there is no getting around the fact that the
     * parameter names must all be unique.  So although a querystring like '?a=4&a=5' is not illegal (and who knows
     * why you would ever want to do it), you can't generate such a thing using http_build_query, which is what the
     * render method below uses to generate the querystring.
     */
    protected array $params = [];

    /**
     * @var int
     * this is the default for http_build_query
     */
    protected int $queryEncoding = PHP_QUERY_RFC1738;

    /**
     * @var ValTesterInterface<string>
     *
     * The http_build_query function has a parameter called 'numeric prefix', which will prepend a string (which
     * must start with a letter) to a numeric array index in order to create a query parameter name.  This class
     * takes a slightly different approach by testing each proposed parameter name before using it. So you can be as
     * restrictive or as lax as you would like in creating parameter names, as long as the parameter names are strings.
     * But in theory, no testing is really required: everything gets url encoded before being transmitted anyway.
     * There are no restrictions on escaped parameter names in the URI specs.
     */
    protected ValTesterInterface $querystringParamNameTester;

    /**
     * getQuerystringParamNameTester
     * @return ValTesterInterface<string>|null
     */
    public function getQuerystringParamNameTester(): ?ValTesterInterface
    {
        return $this->querystringParamNameTester ?? null;
    }

    /**
     * setQuerystringParamNameTester
     * @param ValTesterInterface<string> $querystringParamNameTester
     */
    public function setQuerystringParamNameTester(ValTesterInterface $querystringParamNameTester): void
    {
        $this->querystringParamNameTester = $querystringParamNameTester;
    }

    /**
     * setParams
     * @param array<string, string> $params
     * @throws InvalidQuerystringException
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
     * @throws InvalidQuerystringException
     * @throws InvalidQuerystringParamNameException
     *
     * will overwrite duplicate parameter names
     *
     */
    public function addParam(string $varName, string $value): void
    {
        if (empty($varName)) {
            throw new InvalidQuerystringException();
        }
        $nameTester = $this->getQuerystringParamNameTester();
        if ($nameTester && !$nameTester->testValue($varName)) {
            throw new InvalidQuerystringParamNameException();
        }
        $this->params[$varName] = $value;
    }

    /**
     * getParams
     * @return array<string, string>
     */
    public function getParams(): array
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
