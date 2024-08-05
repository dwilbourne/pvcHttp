<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\http\err;

use pvc\err\XDataTestMaster;
use pvc\http\err\_HttpXData;

/**
 * Class _ValidatorXDataTest
 */
class _HttpXDataTest extends XDataTestMaster
{
    /**
     * testHttpXData
     * @throws \ReflectionException
     * @covers \pvc\http\err\_HttpXData::getLocalXCodes
     * @covers \pvc\http\err\_HttpXData::getXMessageTemplates
     * @covers \pvc\http\err\CurlInitException
     * @covers \pvc\http\err\InvalidPortNumberException
     * @covers \pvc\http\err\InvalidQueryEncodingException
     * @covers \pvc\http\err\InvalidQuerystringParamNameException
     * @covers \pvc\http\err\InvalidQuerystringException
     * @covers \pvc\http\err\MimeTypeCdnException
     * @covers \pvc\http\err\MimeTypesJsonDecodingException
     */
    public function testHttpXData(): void
    {
        $xData = new _HttpXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}