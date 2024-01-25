<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\http\url;

use PHPUnit\Framework\TestCase;
use pvc\http\err\InvalidQueryEncodingException;
use pvc\http\err\InvalidQuerystringParamNameException;
use pvc\http\url\QueryString;
use pvc\interfaces\validator\ValTesterInterface;

class QueryStringTest extends TestCase
{
    protected ValTesterInterface $tester;
    protected QueryString $qstrObject;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->qstrObject = new QueryString($this->tester);
    }

    /**
     * testAddParamThrowExceptionWithInvalidParamName
     * @throws InvalidQuerystringParamNameException
     * @covers \pvc\http\url\QueryString::addParam
     */
    public function testAddParamThrowExceptionWithInvalidParamName(): void
    {
        $this->tester->method('testValue')->willReturn(false);
        $paramName = 'sum_2';
        $paramValue = '3';
        $this->expectException(InvalidQuerystringParamNameException::class);
        $this->qstrObject->addParam($paramName, $paramValue);
    }

    /**
     * testAddParam
     * @throws \pvc\http\err\InvalidQuerystringParamNameException
     * @covers \pvc\http\url\QueryString::addParam
     */
    public function testAddParam(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $paramName = 'sum_2';
        $paramValue = '3';
        $expectedResult = [$paramName => $paramValue];
        $this->qstrObject->addParam($paramName, $paramValue);
        self::assertEqualsCanonicalizing($expectedResult, $this->qstrObject->getParams());
    }

    /**
     * testAddParams
     * @covers \pvc\http\url\QueryString::setParams
     * @covers \pvc\http\url\QueryString::getParams
     */
    public function testAddParams(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $paramName1 = 'sum_2';
        $paramValue1 = '3';

        $paramName2 = 'sum_thing';
        $paramValue2 = 'x';

        $input = [$paramName1 => $paramValue1, $paramName2 => $paramValue2];
        $expectedResult = $input;
        $this->qstrObject->setParams($input);
        self::assertEqualsCanonicalizing($expectedResult, $this->qstrObject->getParams());
    }

    /**
     * testSetEcodingThrowsExceptionWithBadEncodingValue
     * @throws InvalidQueryEncodingException
     * @covers \pvc\http\url\QueryString::setQueryEncoding
     */
    public function testSetEcodingThrowsExceptionWithBadEncodingValue(): void
    {
        $badEncodingValue = 9;
        self::expectException(InvalidQueryEncodingException::class);
        $this->qstrObject->setQueryEncoding($badEncodingValue);
    }

    /**
     * testQueryEncodingDefaultValueIsSet
     * @covers \pvc\http\url\QueryString::__construct
     */
    public function testQueryEncodingDefaultValueIsSet(): void
    {
        self::assertIsInt($this->qstrObject->getQueryEncoding());
    }

    /**
     * testSetgetQueryEncoding
     * @throws InvalidQueryEncodingException
     * @covers \pvc\http\url\QueryString::setQueryEncoding
     * @covers \pvc\http\url\QueryString::getQueryEncoding
     */
    public function testSetgetQueryEncoding(): void
    {
        $encoding = PHP_QUERY_RFC3986;
        $this->qstrObject->setQueryEncoding($encoding);
        self::assertEquals($encoding, $this->qstrObject->getQueryEncoding());
    }

    /**
     * testRenderOnEmptyObject
     * @covers \pvc\http\url\QueryString::render
     */
    public function testRenderOnEmptyObject(): void
    {
        $expectedResult = "";
        self::assertEquals($expectedResult, $this->qstrObject->render());
    }

    /**
     * testRender
     * @covers \pvc\http\url\QueryString::render
     * note that it does not prepend the querystring with a '?'
     */
    public function testRender(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $paramName1 = 'sum_2';
        $paramValue1 = '3';

        $paramName2 = 'sum_thing';
        $paramValue2 = 'x';

        $input = [$paramName1 => $paramValue1, $paramName2 => $paramValue2];
        $expectedResult = $paramName1 . '=' . $paramValue1 . '&' . $paramName2 . '=' . $paramValue2;
        $this->qstrObject->setParams($input);
        self::assertEquals($expectedResult, $this->qstrObject->render());
    }
}
