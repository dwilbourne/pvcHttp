<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\http\mime;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use pvc\http\err\MimeTypesUnknownTypeDetectedException;
use pvc\http\err\MimeTypesUnreadableStreamException;
use pvc\http\mime\MimeTypes;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;
use pvc\storage\filesys\File;

class MimeTypesTest extends TestCase
{
    protected string $fixturesDirectory = __DIR__ . '/fixtures';

    protected MimeTypes $mimeTypes;

    /**
     * @var array<string, MimeTypeInterface>
     */
    protected array $mimeTypeArray;

    /**
     * @var MimeTypesSrcInterface|MockObject
     */
    protected \PHPUnit\Framework\MockObject\MockObject $mimeTypesSrc;

    public function setUp(): void
    {
        $mimeTypeA = $this->createMock(MimeTypeInterface::class);
        $mimeTypeA->method('getMimeTypeName')->willReturn('application/javascript');
        $mimeTypeA->method('getFileExtensions')->willReturn(['js']);

        $mimeTypeB = $this->createMock(MimeTypeInterface::class);
        $mimeTypeB->method('getMimeTypeName')->willReturn('image/jpeg');
        $mimeTypeB->method('getFileExtensions')->willReturn(['jpeg', 'jpg']);

        $this->mimeTypeArray = ['application/javascript' => $mimeTypeA, 'image/jpeg' => $mimeTypeB];

        $this->mimeTypesSrc = $this->createMock(MimeTypesSrcInterface::class);
        $this->mimeTypesSrc->method('getMimeTypes')->willReturn($this->mimeTypeArray);

        $this->mimeTypes = new MimeTypes($this->mimeTypesSrc);
    }

    /**
     * testConstruct
     * @covers \pvc\http\mime\MimeTypes::__construct
     */
    public function testConstructNoCache(): void
    {
        self::assertInstanceOf(MimeTypes::class, $this->mimeTypes);
    }

    /**
     * @return void
     * @covers \pvc\http\mime\MimeTypes::__construct
     */
    public function testConstructWithCache(): void
    {
        $src = $this->createMock(MimeTypesSrcInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects(self::once())->method('set');
        new MimeTypes($src, $cache);
    }

    /**
     * @return void
     * @covers \pvc\http\mime\MimeTypes::getMimeType
     */
    public function testGetMimeType(): void
    {
        $mimeTypeName = 'image/jpeg';
        self::assertInstanceOf(MimeTypeInterface::class, $this->mimeTypes->getMimeType($mimeTypeName));
        $mimeTypeName = 'foo/bar';
        self::assertNull($this->mimeTypes->getMimeType($mimeTypeName));
    }

    /**
     * @return void
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @covers \pvc\http\mime\MimeTypes::getMimeTypes
     */
    public function testGetMimeTypes(): void
    {
        $src = $this->createMock(MimeTypesSrcInterface::class);
        $cache = $this->createMock(CacheInterface::class);
        $cache->expects(self::once())->method('set');
        $expectedResult = ['image/jpeg'];
        $cache->expects(self::once())->method('get')->willReturn(
            $expectedResult
        );
        $mimeTypes = new MimeTypes($src, $cache);
        self::assertEquals($expectedResult, $mimeTypes->getMimeTypes());
    }

    /**
     * testGetMimeTypeNameFromFileExtension
     * @covers \pvc\http\mime\MimeTypes::getMimeTypeNameFromFileExtension
     */
    public function testGetMimeTypeNameFromFileExtension(): void
    {
        $expectedResult = 'image/jpeg';
        self::assertEquals($expectedResult, $this->mimeTypes->getMimeTypeNameFromFileExtension('jpeg'));
        self::assertEquals($expectedResult, $this->mimeTypes->getMimeTypeNameFromFileExtension('jpg'));
        self::assertNull($this->mimeTypes->getMimeTypeNameFromFileExtension('foo'));
    }

    /**
     * testGetFileExtensionsFroimMimeTypeName
     * @covers \pvc\http\mime\MimeTypes::getFileExtensionsFromMimeTypeName
     */
    public function testGetFileExtensionsFromMimeTypeName(): void
    {
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
        self::asserttrue($this->mimeTypes->isValidMimeTypeName('application/javascript'));
        self::assertFalse($this->mimeTypes->isValidMimeTypeName('foo'));
    }

    /**
     * testIsValidMimeTypeExtension
     * @covers \pvc\http\mime\MimeTypes::isValidMimeTypeFileExtension
     */
    public function testIsValidMimeTypeExtension(): void
    {
        self::asserttrue($this->mimeTypes->isValidMimeTypeFileExtension('jpg'));
        self::assertFalse($this->mimeTypes->isValidMimeTypeFileExtension('foo'));
    }

    /**
     * @return void
     * @covers \pvc\http\mime\MimeTypes::detect
     */
    public function testDetectSucceeds(): void
    {
        $testFile = $this->fixturesDirectory . '/' . 'jpeg_with_correct_extension.jpg';
        $fileExtension = pathinfo($testFile, PATHINFO_EXTENSION);
        $expectedMimeTypeString = $this->mimeTypes->getMimeTypeNameFromFileExtension($fileExtension);

        $handle = File::open($testFile);
        $actualMimeTypeString = $this->mimeTypes->detect($handle)->getMimeTypeName();
        File::close($handle);
        self::assertEquals($expectedMimeTypeString, $actualMimeTypeString);
    }

    /**
     * @return void
     * @throws MimeTypesUnreadableStreamException
     * @throws \pvc\http\err\MimeTypesUnknownTypeDetectedException
     * @covers \pvc\http\mime\MimeTypes::detect
     *
     * cannot mock a static method call so we have to use uopz to change the behaviors
     */
    public function testDetectFailsIfUrlIsNotReadable(): void
    {
        $testFile = $this->fixturesDirectory . '/' . 'jpeg_with_correct_extension.jpg';
        $handle = File::open($testFile);
        File::close($handle);

        self::expectException(MimeTypesUnreadableStreamException::class);
        $actualMimeTypeString = $this->mimeTypes->detect($handle)->getMimeTypeName();
        unset($actualMimeTypeString);
    }

    /**
     * @return void
     * @covers \pvc\http\mime\MimeTypes::detect
     */
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    public function testDetectFailsIfMimeTypeIsUnknown(): void
    {
        /**
         * no idea how to mock a file handle - use a real one
         */

        $testFile = $this->fixturesDirectory . '/' . 'jpeg_with_correct_extension.jpg';
        $unknownMimeType = 'bar';
        uopz_set_return('mime_content_type', $unknownMimeType);
        self::expectException(MimeTypesUnknownTypeDetectedException::class);

        $handle = File::open($testFile);
        $actualMimeTypeString = $this->mimeTypes->detect($handle)->getMimeTypeName();
        File::close($handle);

        uopz_unset_return('mime_content_type');

        unset($actualMimeTypeString);
    }
}
