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
     * @covers \pvc\http\err\UnknownMimeTypeDetectedException
     * @covers \pvc\http\err\InvalidUrlException
     * @covers \pvc\http\err\ClientRuntimeException
     * @covers \pvc\http\err\InvalidHttpVerbException
     * @covers \pvc\http\err\InvalidConnectionTimeoutException
     * @covers \pvc\http\err\MimeTypesUnreadableStreamException
     */
    public function testHttpXData(): void
    {
        $xData = new _HttpXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}