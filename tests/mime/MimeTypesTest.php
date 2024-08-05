<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\http\mime;

use PHPUnit\Framework\MockObject\MockObject;
use pvc\http\mime\MimeTypes;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\http\mimetype\MimeTypeInterface;
use pvc\interfaces\http\mimetype\MimeTypesSrcInterface;

class MimeTypesTest extends TestCase
{
    /**
     * @var MimeTypes
     */
    protected MimeTypes $mimeTypes;

    /**
     * @var MimeTypesSrcInterface|MockObject
     */
    protected MimeTypesSrcInterface|MockObject $mimeTypesSrc;

    public function setUp(): void
    {
        $mimeTypeA = $this->createStub(MimeTypeInterface::class);
        $mimeTypeA->method('getMimeTypeName')->willReturn('application/javascript');
        $mimeTypeA->method('getFileExtensions')->willReturn(['js']);

        $mimeTypeB = $this->createStub(MimeTypeInterface::class);
        $mimeTypeB->method('getMimeTypeName')->willReturn('image/jpeg');
        $mimeTypeB->method('getFileExtensions')->willReturn(['jpeg', 'jpg']);

        $mimeTypeArray = ['application/javascript' => $mimeTypeA, 'image/jpeg' => $mimeTypeB];

        $this->mimeTypesSrc = $this->createMock(MimeTypesSrcInterface::class);
        $this->mimeTypesSrc->method('getMimeTypes')->willReturn($mimeTypeArray);
    }

    /**
     * testConstruct
     * @covers \pvc\http\mime\MimeTypes::__construct
     */
    public function testConstruct(): void
    {
        $this->mimeTypesSrc->expects($this->once())->method('initializeMimeTypeData');
        $this->mimeTypes = new MimeTypes($this->mimeTypesSrc);
    }

    /**
     * testGetMimeTypeNameFromFileExtension
     * @covers \pvc\http\mime\MimeTypes::getMimeTypeNameFromFileExtension
     */
    public function testGetMimeTypeNameFromFileExtension(): void
    {
        $this->mimeTypes = new MimeTypes($this->mimeTypesSrc);
        $expectedResult = 'image/jpeg';
        self::assertEquals($expectedResult, $this->mimeTypes->getMimeTypeNameFromFileExtension('jpeg'));
        self::assertEquals($expectedResult, $this->mimeTypes->getMimeTypeNameFromFileExtension('jpg'));
        self::assertNull($this->mimeTypes->getMimeTypeNameFromFileExtension('foo'));
    }

    /**
     * testGetFileExtensionsFroimMimeTypeName
     * @covers \pvc\http\mime\MimeTypes::getFileExtensionsFromMimeTypeName
     */
    public function testGetFileExtensionsFroimMimeTypeName(): void
    {
        $this->mimeTypes = new MimeTypes($this->mimeTypesSrc);
        $expectedResult = ['jpg', 'jpeg'];
        self::assertEqualsCanonicalizing($expectedResult, $this->mimeTypes->getFileExtensionsFromMimeTypeName('image/jpeg'));
        self::assertEmpty($this->mimeTypes->getFileExtensionsFromMimeTypeName('foo'));
    }

    /**
     * testIsValidMimeTypeName
     * @covers \pvc\http\mime\MimeTypes::isValidMimeTypeName
     */
    public function testIsValidMimeTypeName(): void
    {
        $this->mimeTypes = new MimeTypes($this->mimeTypesSrc);
        self::asserttrue($this->mimeTypes->isValidMimeTypeName('application/javascript'));
        self::assertFalse($this->mimeTypes->isValidMimeTypeName('foo'));
    }
}
