<?php
/**
 * @package: pvc
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 * @version: 1.0
 */

namespace tests\url;

use PHPUnit\Framework\TestCase;
use pvc\url\Url;

class UrlTest extends TestCase
{

    protected Url $url;
    protected array $testArray;
    protected string $testResult;

    function setUp(): void
    {
        $this->url = new Url();

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

    public function testSetGetScheme(): void
    {
        $scheme = 'ftp';
        $this->url->setScheme($scheme);
        self::assertEquals($scheme, $this->url->getScheme());
    }

    public function testSetGetHost(): void
    {
        $host = 'google.com';
        $this->url->setHost($host);
        self::assertEquals($host, $this->url->getHost());
    }

    public function testSetGetPort(): void
    {
        $port = '443';
        $this->url->setPort($port);
        self::assertEquals($port, $this->url->getPort());
    }

    public function testSetGetUser(): void
    {
        $user = 'someuser';
        $this->url->setUser($user);
        self::assertEquals($user, $this->url->getUser());
    }

    public function testSetGetPassword(): void
    {
        $password = 'somepassword';
        $this->url->setPassword($password);
        self::assertEquals($password, $this->url->getPassword());
    }

    public function testSetGetPath(): void
    {
        $path = "/path/to/some/resource";
        $this->url->setPath($path);
        self::assertEquals($path, $this->url->getPath());
    }

    public function testSetGetEmptyPath(): void
    {
        $path = '';
        $this->url->setPath($path);
        self::assertEquals('', $this->url->getPath());
    }

    public function testSetGetPathAsArray(): void
    {
        $path = "path/to/some/resource";
        $this->url->setPath($path);
        $expectedResult = ['path', 'to', 'some', 'resource'];
        self::assertEquals($expectedResult, $this->url->getPathAsArray());
    }

    public function testSetGetPathAsString(): void
    {
        $path = "path/to/some/resource";
        $this->url->setPath($path);
        self::assertEquals($path, $this->url->getPath());
    }

    public function testSetGetQuery(): void
    {
        $query = 'axe=1&shovel=2';
        $this->url->setQuery($query);
        self::assertEquals($query, $this->url->getQuery());
    }

    public function testSetGetFragment(): void
    {
        $fragment = "anchor";
        $this->url->setFragment($fragment);
        self::assertEquals($fragment, $this->url->getFragment());
    }

    public function testSetGetAttributesFromArray(): void
    {
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

    public function testGenerateUrlString(): void
    {
        $this->url->setAttributesFromArray($this->testArray);
        self::assertEquals($this->testResult, $this->url->generateURLString());
    }

    public function testNotExist(): void
    {
        $this->url->setScheme('http');
        $this->url->setHost('somebadhost');
        $this->assertFalse($this->url->exists());
    }

    public function testExists(): void
    {
        $this->url->setScheme('http');
        $this->url->setHost('www.google.com');
        $this->assertTrue($this->url->exists());
    }


}
