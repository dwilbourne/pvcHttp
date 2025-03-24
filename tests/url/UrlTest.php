<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\http\url;

use PHPUnit\Framework\TestCase;
use pvc\http\err\CurlInitException;
use pvc\http\err\InvalidPortNumberException;
use pvc\http\err\InvalidQuerystringException;
use pvc\http\url\Url;
use pvc\interfaces\http\QueryStringInterface;
use pvc\interfaces\parser\ParserQueryStringInterface;

class UrlTest extends TestCase
{

    protected Url $url;

    protected ParserQueryStringInterface $parserQueryString;

    protected array $testArray;

    protected string $testResult;

    function setUp(): void
    {
        $this->parserQueryString = $this->createMock(ParserQueryStringInterface::class);
        $this->url = new Url($this->parserQueryString);

        $this->testArray = array(
            'scheme' => 'https',
            'host' => 'ajax.googleapis.com',
            'port' => '443',
            'user' => 'someuser',
            'password' => 'somepassword',
            'path' => 'ajax/libs/jquery/3.5.1/jquery.min.js',
            'query' => 'axe=1&shovel=2',
            'fragment' => 'anchor'
        );

        $this->testResult = '';
        $this->testResult .= 'https://someuser:somepassword@ajax.googleapis.com:443';
        $this->testResult .= '/ajax/libs/jquery/3.5.1/jquery.min.js?axe=1&shovel=2#anchor';
    }

    /**
     * testConstruct
     * @covers \pvc\http\url\Url::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(Url::class, $this->url);
    }

    /**
     * testSetGetParserQueryString
     * @covers \pvc\http\url\Url::setParserQueryString()
     * @covers \pvc\http\url\Url::getParserQueryString()
     */
    public function testSetGetParserQueryString(): void
    {
        $parser = $this->createMock(ParserQueryStringInterface::class);
        $this->url->setParserQueryString($parser);
        self::assertEquals($parser, $this->url->getParserQueryString());
    }

    /**
     * testSetGetScheme
     * @covers \pvc\http\url\Url::setScheme
     * @covers \pvc\http\url\Url::getScheme
     */
    public function testSetGetScheme(): void
    {
        $scheme = 'ftp';
        $this->url->setScheme($scheme);
        self::assertEquals($scheme, $this->url->getScheme());
    }

    /**
     * testSetGetHost
     * @covers \pvc\http\url\Url::setHost
     * @covers \pvc\http\url\Url::getHost
     */
    public function testSetGetHost(): void
    {
        $host = 'google.com';
        $this->url->setHost($host);
        self::assertEquals($host, $this->url->getHost());
    }

    /**
     * testSetPortThrowsExceptionWithBadPortNumber
     * @throws InvalidPortNumberException
     * @covers \pvc\http\url\Url::setPort
     */
    public function testSetPortThrowsExceptionWithBadPortNumber(): void
    {
        $badPortNumber = '12hj';
        self::expectException(InvalidPortNumberException::class);
        $this->url->setPort($badPortNumber);
    }

    /**
     * testSetGetPort
     * @covers \pvc\http\url\Url::setPort
     * @covers \pvc\http\url\Url::getPort
     */
    public function testSetGetPort(): void
    {
        $port = '443';
        $this->url->setPort($port);
        self::assertEquals($port, $this->url->getPort());
    }

    /**
     * testSetGetUser
     * @covers \pvc\http\url\Url::setUser
     * @covers \pvc\http\url\Url::getUser
     */
    public function testSetGetUser(): void
    {
        $user = 'someuser';
        $this->url->setUser($user);
        self::assertEquals($user, $this->url->getUser());
    }

    /**
     * testSetGetPassword
     * @covers \pvc\http\url\Url::setPassword
     * @covers \pvc\http\url\Url::getPassword
     */
    public function testSetGetPassword(): void
    {
        $password = 'somepassword';
        $this->url->setPassword($password);
        self::assertEquals($password, $this->url->getPassword());
    }

    /**
     * testSetGetPath
     * @covers \pvc\http\url\Url::setPath
     * @covers \pvc\http\url\Url::getPath
     */
    public function testSetGetPath(): void
    {
        $path = "/path/to/some/resource";
        $this->url->setPath($path);
        self::assertEquals($path, $this->url->getPath());
    }

    /**
     * testSetGetEmptyPath
     * @covers \pvc\http\url\Url::setPath
     * @covers \pvc\http\url\Url::getPath
     */
    public function testSetGetEmptyPath(): void
    {
        $path = '';
        $this->url->setPath($path);
        self::assertEquals('', $this->url->getPath());
    }

    /**
     * testSetGetPathAsArray
     * @covers \pvc\http\url\Url::getPathAsArray
     */
    public function testSetGetPathAsArray(): void
    {
        $path = "path/to/some/resource";
        $this->url->setPath($path);
        $expectedResult = ['path', 'to', 'some', 'resource'];
        self::assertEquals($expectedResult, $this->url->getPathAsArray());
    }

    /**
     * testSetGetPathAsString
     * @covers \pvc\http\url\Url::getPath
     */
    public function testSetGetPathAsString(): void
    {
        $path = "path/to/some/resource";
        $this->url->setPath($path);
        self::assertEquals($path, $this->url->getPath());
    }

    /**
     * testSetGetQuery
     * @covers \pvc\http\url\Url::setQuery
     * @covers \pvc\http\url\Url::getQuery
     */
    public function testSetGetQuery(): void
    {
        $query = 'axe=1&shovel=2';
        $parserReturnValue = true;
        $this->setMockParserQueryString($query, $parserReturnValue);
        $this->url->setQuery($query);
        self::assertEquals($query, $this->url->getQuery());
    }

    /**
     * testSetQueryThrowsExceptionWhenParserFails
     * @throws InvalidQuerystringException
     * @covers \pvc\http\url\Url::setQuery()
     */
    public function testSetQueryThrowsExceptionWhenParserFails(): void
    {
        $query = 'axe=1&shovel=2';
        $queryStringObject = $this->createMock(QueryStringInterface::class);
        $this->parserQueryString->setQueryString($queryStringObject);
        $this->parserQueryString->expects($this->once())->method('parse')->with($query)->willReturn(false);
        self::expectException(InvalidQuerystringException::class);
        $this->url->setQuery($query);
    }

    /**
     * testSetGetFragment
     * @covers \pvc\http\url\Url::setFragment
     * @covers \pvc\http\url\Url::getFragment
     */
    public function testSetGetFragment(): void
    {
        $fragment = "anchor";
        $this->url->setFragment($fragment);
        self::assertEquals($fragment, $this->url->getFragment());
    }

    /**
     * testSetGetAttributesFromArray
     * @covers \pvc\http\url\Url::setAttributesFromArray
     */
    public function testSetGetAttributesFromArray(): void
    {
        $query = $this->testArray['query'];

        $this->setMockParserQueryString($query);

        $this->url->setAttributesFromArray($this->testArray);
        self::assertEquals($this->testArray['scheme'], $this->url->getScheme());
        self::assertEquals($this->testArray['host'], $this->url->getHost());
        self::assertEquals($this->testArray['port'], $this->url->getPort());
        self::assertEquals($this->testArray['user'], $this->url->getUser());
        self::assertEquals($this->testArray['password'], $this->url->getPassword());
        self::assertEquals($this->testArray['path'], $this->url->getPath());
        self::assertEquals($this->testArray['query'], $this->url->getQuery());
        self::assertEquals($this->testArray['fragment'], $this->url->getFragment());
    }

    /**
     * testGenerateUrlString
     * @covers \pvc\http\url\Url::generateURLString
     */
    public function testGenerateUrlString(): void
    {
        $query = $this->testArray['query'];

        $this->setMockParserQueryString($query);

        $this->url->setAttributesFromArray($this->testArray);
        $encoded = false;
        self::assertEquals($this->testResult, $this->url->generateURLString($encoded));
    }

    /**
     * testNotExist
     * @throws \pvc\http\err\CurlInitException
     * @covers \pvc\http\url\Url::exists
     * @covers \pvc\http\url\Url::getCurlErrorMessage
     */
    public function testNotExist(): void
    {
        $this->url->setScheme('http');
        $this->url->setHost('somebadhost');
        $this->assertFalse($this->url->exists());
        self::assertNotEmpty($this->url->getCurlErrorMessage());
    }

    /**
     * testMakeCurlInitFail
     * @throws CurlInitException
     * @covers \pvc\http\url\Url::exists
     * @runInSeparateProcess
     */
    public function testThrowsExceptionWhenCurlInitReturnsFalse(): void
    {
        $this->url->setScheme('http');
        $this->url->setHost('somebadhost');
        uopz_set_return('curl_init', false);
        self::expectException(CurlInitException::class);
        $this->url->exists();
        uopz_unset_return('curl_init');
    }

    /**
     * testExists
     * @throws \pvc\http\err\CurlInitException
     * @covers \pvc\http\url\Url::exists
     * @covers \pvc\http\url\Url::getHttpStatusCode
     * @covers \pvc\http\url\Url::getHttpStatus
     */
    public function testExists(): void
    {
        $this->url->setScheme('http');
        $this->url->setHost('www.google.com');
        $this->assertTrue($this->url->exists());
        self::assertEquals(200, $this->url->getHttpStatusCode());
        $expectedStatus = 'OK';
        self::assertEquals($expectedStatus, $this->url->getHttpStatus());
        self::assertEmpty($this->url->getCurlErrorMessage());
    }

    /**
     * setMockParserQueryString
     * @param string $query
     */
    protected function setMockParserQueryString(string $query): void
    {
        $queryStringObject = $this->createMock(QueryStringInterface::class);
        $this->parserQueryString->setQueryString($queryStringObject);
        $this->parserQueryString->expects($this->once())->method('parse')->with($query)->willReturn(true);
        $this->parserQueryString->expects($this->once())->method('getParsedValue')->willReturn($queryStringObject);
        $queryStringObject->expects($this->once())->method('render')->willReturn($query);
    }


}
