<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare(strict_types=1);

namespace pvcTests\http\err;

use PHPUnit\Framework\Attributes\CoversClass;
use pvc\err\XDataTestMaster;
use pvc\http\err\_HttpXData;
use pvc\http\err\InvalidHttpVerbException;
use pvc\http\err\MimeTypeCdnException;
use pvc\http\err\MimeTypesJsonDecodingException;
use pvc\http\err\MimeTypesUnreadableStreamException;
use pvc\http\err\UnknownMimeTypeDetectedException;

/**
 * Class _ValidatorXDataTest
 */
#[CoversClass(MimeTypeCdnException::class)]
#[CoversClass(MimeTypesJsonDecodingException::class)]
#[CoversClass(UnknownMimeTypeDetectedException::class)]
#[CoversClass(InvalidHttpVerbException::class)]
#[CoversClass(MimeTypesUnreadableStreamException::class)]
class _HttpXDataTest extends XDataTestMaster
{
    /**
     * testHttpXData
     * @throws \ReflectionException
     * @covers \pvc\http\err\_HttpXData::getLocalXCodes
     * @covers \pvc\http\err\_HttpXData::getXMessageTemplates
     */
    public function testHttpXData(): void
    {
        $xData = new _HttpXData();
        self::assertTrue($this->verifyLibrary($xData));
    }
}