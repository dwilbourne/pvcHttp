<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version 1.0
 */

namespace tests\url;


use PHPUnit\Framework\TestCase;
use pvc\parser\ParserInterface;
use pvc\url\Url;


/**
 * Class UrlParserTest
 * @package tests\url
 *
 * As of this writing, there are two url parsers in the pvc framework: one which is based on a regular expression
 * (found in the regex library) and one which is based on parse_url (found in the parser library).  This test
 * can be (should be) used for any url parser.  The implementation-specific test is a simple extension of
 * this class using the implementation specific parser in the constructor.
 *
 */
abstract class UrlParserTest extends TestCase
{
    protected ParserInterface $urlParser;

    public function setUrlParser(ParserInterface $urlParser) : void
    {
        $this->urlParser = $urlParser;
    }

    private function parseUrl(string $url) : Url
    {
        self::assertTrue($this->urlParser->parse($url));
        return $this->urlParser->getParsedValue();
    }

    public function testParseUrlStringBasic() {
        $urlString = 'http://username:password@hostname:9090/path?arg=value#anchor';
        $values = array(
            'scheme' => 'http',
            'host' => 'hostname',
            'port' => '9090',
            'user' => 'username',
            'pass' => 'password',
            'path' => '/path',
            'query' => 'arg=value',
            'fragment' => 'anchor'
        );
        $expectedResult = new Url($values);
        $result = $this->parseUrl($urlString);
        self::assertTrue($expectedResult == $result);
    }

    public function testParseUrlStringReservedChars() {
        $reservedChars = ['!', '*', '\'', '(', ')', ':', ';', '@', '&', '=', '+', '$', ',', '/', '?', '#', '[', ']' ];

        // reserved chars should be in the path component
        $url = 'http://www.somehost.com/' . implode($reservedChars);

        $result = $this->parseUrl($url);

        self::assertEquals('http', $result['scheme']);
        self::assertEquals('www.somehost.com', $result['host']);

        // the '?' which is four characters from the end is interpreted as the delimiter for a querystring.  In order to use these characters
        // without risking that the parsing be messed up, they should all be url encoded
        self::assertNotEquals('/' . implode($reservedChars), $result['path']);

        $url = 'http://www.somehost.com/' . urlencode(implode($reservedChars));
        $result = $this->parseUrl($url);
        self::assertEquals('/' . urlencode(implode($reservedChars)), $result['path']);
    }

    /**
     * createUrlStringMultibyteChars
     * not all parsers can deal with multibyte characters in the string.  Use this function to create
     * a UTF-8 encoded url where the path component contains a multibyte character
     */
    public function createUrlStringMultibyteChars() : string
    {
        $stringWithUnicode = 'Hello/World' . mb_chr('\u263A', 'UTF-8');
        return 'http://www.nowhere.com/' . $stringWithUnicode;
    }

    public function testBadUrls() {
        $badUrls = ["http:///example.com", "http://:80", "http://user@:80"];
        foreach($badUrls as $badUrl) {
            self::assertFalse($this->urlParser->parse($badUrl));
        }

    }


}