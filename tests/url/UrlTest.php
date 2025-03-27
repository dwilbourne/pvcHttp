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

class UrlTest extends TestCase
{

    protected Url $url;

    protected QueryStringInterface $queryString;

    protected array $testArray;

    protected string $testResult;

    function setUp(): void
    {
        $this->queryString = $this->createMock(QueryStringInterface::class);
        $this->queryString->method('render')->willReturn('axe=1&shovel=2');
        $this->url = new Url($this->queryString);

        $this->testArray = array(
            'scheme' => 'https',
            'host' => 'ajax.googleapis.com',
            'port' => '443',
            'user' => 'someuser',
            'password' => 'somepassword',
            'path' => 'ajax/libs/jquery/3.5.1/jquery.min.js',
            'query' => $this->queryString,
            'fragment' => 'anchor'
        );

        $this->testResult = '';
        $this->testResult .= 'https://someuser:somepassword@ajax.googleapis.com:443';
        $this->testResult .= '/ajax/libs/jquery/3.5.1/jquery.min.js?' . $this->queryString->render() . '#anchor';
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
     * @covers \pvc\http\url\Url::setQueryString
     * @covers \pvc\http\url\Url::getQueryString
     */
    public function testSetGetQueryString(): void
    {
        self::assertEquals($this->queryString, $this->url->getQueryString());
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
     * @covers \pvc\http\url\Url::parseQueryString
     */
    public function testSetGetAttributesFromArray(): void
    {
        /**
         * for this method, the query key in this array should be a string of query parameters
         */
        $this->testArray['query'] = $this->queryString->render();
        $this->queryString->expects($this->exactly(2))->method('addParam');
        $this->url->setAttributesFromArray($this->testArray);
        self::assertEquals($this->testArray['scheme'], $this->url->getScheme());
        self::assertEquals($this->testArray['host'], $this->url->getHost());
        self::assertEquals($this->testArray['port'], $this->url->getPort());
        self::assertEquals($this->testArray['user'], $this->url->getUser());
        self::assertEquals($this->testArray['password'], $this->url->getPassword());
        self::assertEquals($this->testArray['path'], $this->url->getPath());
        self::assertEquals($this->testArray['query'], $this->url->getQueryString()->render());
        self::assertEquals($this->testArray['fragment'], $this->url->getFragment());
    }

    /**
     * @return void
     * @throws InvalidQuerystringException
     * @covers \pvc\http\url\Url::parseQueryString
     */
    public function testParseBadQueryString(): void
    {
        $badString = 'a=1=3';
        $this->testArray['query'] = $badString;
        self::expectException(InvalidQuerystringException::class);
        $this->url->setAttributesFromArray($this->testArray);
    }

    /**
     * @return void
     * @throws CurlInitException
     * @covers \pvc\http\url\Url::sendRequest
     * @runInSeparateProcess
     */
    public function testSendRequestThrowsExceptionWhenCurlInitFails(): void
    {
        $this->expectException(CurlInitException::class);
        uopz_set_return('curl_init', false);
        $this->url->sendRequest();
        uopz_unset_return('curl_init');
    }

    /**
     * testGenerateUrlString
     * @covers \pvc\http\url\Url::generateURLString
     */
    public function testGenerateUrlString(): void
    {
        $this->testArray['query'] = $this->queryString->render();
        $this->url->setAttributesFromArray($this->testArray);
        $encoded = false;
        self::assertEquals($this->testResult, $this->url->generateURLString($encoded));
    }

    /**
     * testNotExist
     * @throws \pvc\http\err\CurlInitException
     * @covers \pvc\http\url\Url::sendRequest
     * @covers \pvc\http\url\Url::getCurlErrorMessage
     */
    public function testNotExist(): void
    {
        $this->url->setScheme('http');
        /**
         * unable to resolve the hostname causes curl_exec to fail
         */
        $this->url->setHost('somebadhost');
        $this->assertEquals(-1, $this->url->sendRequest());
        self::assertNotEmpty($this->url->getCurlErrorMessage());

        /**
         * which is different from a 404 'page not found'
         */
        $this->url->setHost('google.com');
        $this->url->setPath('/foobarbaz');
        $this->assertEquals(404, $this->url->sendRequest());
        self::assertEmpty($this->url->getCurlErrorMessage());
    }

    /**
     * testSendRequest
     * @throws \pvc\http\err\CurlInitException
     * @covers \pvc\http\url\Url::sendRequest
     */
    public function testSendRequest(): void
    {
        $this->url->setScheme('http');
        $this->url->setHost('www.google.com');
        $expectedStatusCode = 200;
        $this->assertEquals($expectedStatusCode, $this->url->sendRequest());
        self::assertEmpty($this->url->getCurlErrorMessage());
    }

    /**
     * @return void
     * @covers \pvc\http\url\Url::getHttpStatusFromCode
     */
    public function testGetStatusFromCode(): void
    {
        self::assertEquals('OK', $this->url->getHttpStatusFromCode(200));
        self::assertNull($this->url->getHttpStatusFromCode(905));
    }
}
