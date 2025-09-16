<?php
/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvcTests\http\url;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\http\err\InvalidQueryEncodingException;
use pvc\http\err\InvalidQuerystringException;
use pvc\http\err\InvalidQuerystringParamNameException;
use pvc\http\url\QueryString;
use pvc\interfaces\validator\ValTesterInterface;

#[\PHPUnit\Framework\Attributes\CoversClass(\pvc\http\url\QueryString::class)]
class QueryStringTest extends TestCase
{
    /**
     * @var ValTesterInterface<string>|MockObject
     */
    protected \PHPUnit\Framework\MockObject\MockObject $tester;
    protected QueryString $qstrObject;

    public function setUp(): void
    {
        $this->tester = $this->createMock(ValTesterInterface::class);
        $this->qstrObject = new QueryString();
    }

    /**
     * testGetQuerystringParamNameTesterReturnsNullWhenNotSet
     * @covers \pvc\http\url\QueryString::getQuerystringParamNameTester
     */
    public function testGetQuerystringParamNameTesterReturnsNullWhenNotSet(): void
    {
        self::assertNull($this->qstrObject->getQuerystringParamNameTester());
    }

    /**
     * testSetGetParamNameTester
     * @covers \pvc\http\url\QueryString::setQuerystringParamNameTester
     * @covers \pvc\http\url\QueryString::getQuerystringParamNameTester
     */
    public function testSetGetParamNameTester(): void
    {
        $this->qstrObject->setQuerystringParamNameTester($this->tester);
        self::assertEquals($this->tester, $this->qstrObject->getQuerystringParamNameTester());
    }

    /**
     * testAddParamThrowExceptionWithInvalidParamName
     * @throws InvalidQuerystringParamNameException
     * @covers \pvc\http\url\QueryString::setParam
     */
    public function testAddParamThrowExceptionWithInvalidParamName(): void
    {
        $this->tester->method('testValue')->willReturn(false);
        $this->qstrObject->setQuerystringParamNameTester($this->tester);
        $paramName = 'sum_2';
        $paramValue = '3';
        $this->expectException(InvalidQuerystringParamNameException::class);
        $this->qstrObject->setParam($paramName, $paramValue);
    }

    /**
     * @return void
     * @throws InvalidQuerystringException
     * @throws InvalidQuerystringParamNameException
     * @covers \pvc\http\url\QueryString::setParam
     */
    public function testAddParamThrowsExceptionWithEmptyParamName(): void
    {
        $paramName = '';
        $paramValue = '3';
        $this->expectException(InvalidQuerystringException::class);
        $this->qstrObject->setParam($paramName, $paramValue);
    }

    /**
     * testAddParam
     * @throws \pvc\http\err\InvalidQuerystringParamNameException
     * @covers \pvc\http\url\QueryString::setParam
     */
    public function testAddParam(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $this->qstrObject->setQuerystringParamNameTester($this->tester);
        $paramName = 'sum_2';
        $paramValue = '3';
        $expectedResult = [$paramName => $paramValue];
        $this->qstrObject->setParam($paramName, $paramValue);
        self::assertEqualsCanonicalizing($expectedResult, $this->qstrObject->getParams());
    }

    /**
     * testSetParams
     * @covers \pvc\http\url\QueryString::setParams
     * @covers \pvc\http\url\QueryString::getParams
     */
    public function testSetParams(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $this->qstrObject->setQuerystringParamNameTester($this->tester);
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
     * testSetParamsWithEmptyValue
     * @throws InvalidQuerystringParamNameException
     * @covers \pvc\http\url\QueryString::setParams
     * @covers \pvc\http\url\QueryString::getParams
     */
    public function testSetParamsWithEmptyValue(): void
    {
        $this->tester->method('testValue')->willReturn(true);
        $this->qstrObject->setQuerystringParamNameTester($this->tester);
        $paramName1 = 'sum_2';
        $paramValue1 = '';
        $expectedResult = [$paramName1 => $paramValue1];
        $this->qstrObject->setParams($expectedResult);
        self::assertEqualsCanonicalizing($expectedResult, $this->qstrObject->getParams());
    }

    /**
     * testSetParamsWithEmptyParamNameThrowsException
     * @throws InvalidQuerystringParamNameException
     * @covers \pvc\http\url\QueryString::setParams
     */
    public function testSetParamsWithEmptyParamNameThrowsException(): void
    {
        $paramName1 = '';
        $paramValue1 = 'nothing';
        $expectedResult = [$paramName1 => $paramValue1];
        self::expectException(InvalidQuerystringException::class);
        $this->qstrObject->setParams($expectedResult);
    }

    /**
     * testSetEncodingThrowsExceptionWithBadEncodingValue
     * @throws InvalidQueryEncodingException
     * @covers \pvc\http\url\QueryString::setQueryEncoding
     */
    public function testSetEncodingThrowsExceptionWithBadEncodingValue(): void
    {
        $badEncodingValue = 9;
        self::expectException(InvalidQueryEncodingException::class);
        $this->qstrObject->setQueryEncoding($badEncodingValue);
    }

    /**
     * testQueryEncodingDefaultValueIsSet
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

    /**
     * testRenderReturnsEmptyStringWhenThereAreNoParameters
     * @covers \pvc\http\url\QueryString::render
     */
    public function testRenderReturnsEmptyStringWhenThereAreNoParameters(): void
    {
        self::assertEquals('', $this->qstrObject->render());
    }
}
