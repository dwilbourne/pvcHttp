<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\http\url;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\http\err\InvalidUrlException;
use pvc\http\url\Url;
use pvc\interfaces\http\QueryStringInterface;
use pvc\interfaces\parser\ParserQueryStringInterface;
use pvc\interfaces\validator\ValTesterInterface;

/**
 * @phpstan-import-type UrlShape from Url
 */
class UrlTest extends TestCase
{

    protected Url $url;

    protected ParserQueryStringInterface $queryStringParser;

    protected QueryStringInterface|MockObject $queryString;

    /**
     * @var  ValTesterInterface<string>|MockObject
     */
    protected ValTesterInterface|MockObject $urlTester;

    /**
     * @var UrlShape
     */
    protected array $testArray;

    protected string $testUrlString;

    function setUp(): void
    {
        $this->queryString = $this->createMock(QueryStringInterface::class);
        $this->queryString->method('render')->willReturn('axe=1&shovel=2');

        $this->queryStringParser = $this->createMock(ParserQueryStringInterface::class);
        $this->queryStringParser->method('getParsedValue')->willReturn($this->queryString);
        $this->urlTester = $this->createMock(ValTesterInterface::class);
        $this->url = new Url($this->queryStringParser, $this->urlTester);

        /**
         * @var UrlShape
         */
        $this->testArray = array(
            'scheme' => 'https',
            'host' => 'ajax.googleapis.com',
            'port' => '443',
            'user' => 'someuser',
            'password' => 'somepassword',
            'path' => '/ajax/libs/jquery/3.5.1/jquery.min.js',
            'query' => $this->queryString->render(),
            'fragment' => 'anchor'
        );

        $this->testUrlString = '';
        $this->testUrlString .= 'https://someuser:somepassword@ajax.googleapis.com:443';
        $this->testUrlString .= '/ajax/libs/jquery/3.5.1/jquery.min.js?' . $this->queryString->render() . '#anchor';
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
     * testSetGetQuery
     * @covers \pvc\http\url\Url::getQueryString
     */
    public function testSetGetQueryString(): void
    {
        self::assertEquals($this->queryString, $this->url->getQueryString());
    }

    /**
     * testHydrate
     * @covers \pvc\http\url\Url::hydrateFromArray
     */
    public function testHydrateFromArray(): void
    {
        $this->urlTester->method('testValue')->willReturn(true);
        $this->url->hydrateFromArray($this->testArray);

        self::assertEquals($this->testArray['scheme'], $this->url->scheme);
        self::assertEquals($this->testArray['host'], $this->url->host);
        self::assertEquals($this->testArray['port'], $this->url->port);
        self::assertEquals($this->testArray['user'], $this->url->user);
        self::assertEquals($this->testArray['password'], $this->url->password);
        self::assertEquals($this->testArray['path'], $this->url->path);
        self::assertEquals($this->testArray['query'], $this->url->getQueryString()->render());
        self::assertEquals($this->testArray['fragment'], $this->url->fragment);
    }

    /**
     * @return void
     * @throws \pvc\http\err\InvalidUrlException
     * @covers \pvc\http\url\Url::render
     */
    public function testRender(): void
    {
        $this->urlTester->method('testValue')->willReturn(true);
        $this->url->hydrateFromArray($this->testArray);
        $expectedResult = $this->testUrlString;
        self::assertEquals($expectedResult, $this->url->render());
    }

    public function testBadUrlCannotBerenderedByDefault(): void
    {
        $this->url->fragment = 'someFragment';
        self::expectException(InvalidUrlException::class);
        $this->url->render();
    }
}
