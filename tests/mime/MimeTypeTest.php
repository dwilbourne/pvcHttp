<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\http\mime;

use PHPUnit\Framework\TestCase;
use pvc\http\mime\MimeType;

class MimeTypeTest extends TestCase
{
    protected MimeType $mimeType;

    public function setUp(): void
    {
        $this->mimeType = new MimeType();
    }

    /**
     * testSetGetMimeTypeName
     * @covers \pvc\http\mime\MimeType::getMimeTypeName
     * @covers \pvc\http\mime\MimeType::setMimeTypeName
     */
    public function testSetGetMimeTypeName(): void
    {
        $testName = 'text/css';
        self::assertNull($this->mimeType->getMimeTypeName());
        $this->mimeType->setMimeTypeName($testName);
        self::assertEquals($testName, $this->mimeType->getMimeTypeName());
    }

    /**
     * testSetGetFileExtensions
     * @covers \pvc\http\mime\MimeType::getFileExtensions
     * @covers \pvc\http\mime\MimeType::setFileExtensions
     */
    public function testSetGetFileExtensions(): void
    {
        $testFileExtensions = ['htm', 'html'];
        self::assertEmpty($this->mimeType->getFileExtensions());
        $this->mimeType->setFileExtensions($testFileExtensions);
        self::assertEquals($testFileExtensions, $this->mimeType->getFileExtensions());
    }

}
