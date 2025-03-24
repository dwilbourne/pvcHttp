<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\http\mime;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use pvc\err\pvc\file\FileDoesNotExistException;
use pvc\err\pvc\file\FileNotReadableException;
use pvc\http\err\ConflictingMimeTypesException;
use pvc\http\err\InvalidMimeDetectionConstantException;
use pvc\http\err\UnknownMimeTypeDetectedException;
use pvc\http\mime\MimeType;
use pvc\http\mime\MimeTypeFactory;
use pvc\http\mime\MimeTypes;
use pvc\http\mime\MimeTypesSrc;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;

class MimeTypesTest extends TestCase
{
    protected string $fixturesDirectory = __DIR__ . '/fixtures';

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
        $mimeTypeA = $this->createMock(MimeTypeInterface::class);
        $mimeTypeA->method('getMimeTypeName')->willReturn('application/javascript');
        $mimeTypeA->method('getFileExtensions')->willReturn(['js']);

        $mimeTypeB = $this->createMock(MimeTypeInterface::class);
        $mimeTypeB->method('getMimeTypeName')->willReturn('image/jpeg');
        $mimeTypeB->method('getFileExtensions')->willReturn(['jpeg', 'jpg']);

        $mimeTypeArray = ['application/javascript' => $mimeTypeA, 'image/jpeg' => $mimeTypeB];

        $this->mimeTypesSrc = $this->createMock(MimeTypesSrcInterface::class);
        $this->mimeTypesSrc->method('getMimeTypes')->willReturn($mimeTypeArray);
        $this->mimeTypes = new MimeTypes($this->mimeTypesSrc);
    }

    /**
     * testConstruct
     * @covers \pvc\http\mime\MimeTypes::__construct
     */
    public function testConstruct(): void
    {
        $this->mimeTypesSrc->expects($this->once())->method('initializeMimeTypeData');
        $mimeTypes = new MimeTypes($this->mimeTypesSrc);
        self::assertInstanceOf(MimeTypes::class, $mimeTypes);
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
     * @throws FileDoesNotExistException
     * @throws \pvc\err\pvc\file\FileNotReadableException
     * @throws \pvc\http\err\ConflictingMimeTypesException
     * @throws \pvc\http\err\InvalidMimeDetectionConstantException
     * @covers \pvc\http\mime\MimeTypes::detect
     */
    public function testDetectThrowsExceptionWithNonExistentFile(): void
    {
        $nonExistentFile = 'non_existent_file';
        $detectionMethod = MimeTypes::DETECT_FROM_CONTENTS;
        self::expectException(FileDoesNotExistException::class);
        $this->mimeTypes->detect($nonExistentFile, $detectionMethod);
    }

    /**
     * @return void
     * @throws FileDoesNotExistException
     * @throws FileNotReadableException
     * @throws \pvc\http\err\ConflictingMimeTypesException
     * @throws \pvc\http\err\InvalidMimeDetectionConstantException
     * @covers \pvc\http\mime\MimeTypes::detect
     */
    public function testDetectThrowsExceptionIfFileIsNotReadable(): void
    {
        $unreadableFile = $this->fixturesDirectory . '/' . 'unreadable_file.php';
        /**
         * no permissions to the file for group and world.
         * chmod($unreadableFile, 0600);
         * developing on Windows, permissions to this file removed manually
         */

        $detectionMethod = MimeTypes::DETECT_FROM_CONTENTS;
        self::expectException(FileNotReadableException::class);
        $this->mimeTypes->detect($unreadableFile, $detectionMethod);
    }

    /**
     * @return void
     * @throws FileDoesNotExistException
     * @throws FileNotReadableException
     * @throws InvalidMimeDetectionConstantException
     * @throws \pvc\http\err\ConflictingMimeTypesException
     * @covers \pvc\http\mime\MimeTypes::detect
     * @covers \pvc\http\mime\MimeTypes::validateMimeTypeDetectionMethods
     */
    public function testDetectThrowsExceptionWithBadDetectionMethod(): void
    {
        $testFile = $this->fixturesDirectory . '/' . 'jpeg_with_correct_extension.jpg';
        $detectionMethod = 0;
        self::expectException(InvalidMimeDetectionConstantException::class);
        $this->mimeTypes->detect($testFile, $detectionMethod);
    }

    /**
     * @return void
     * @throws ConflictingMimeTypesException
     * @throws FileDoesNotExistException
     * @throws FileNotReadableException
     * @throws InvalidMimeDetectionConstantException
     * @covers \pvc\http\mime\MimeTypes::detect
     * @runInSeparateProcess
     */
    public function testDetectThrowsExceptionWhenContentsAndFileExtensionConflict(): void
    {
        $testFile = $this->fixturesDirectory . '/' . 'jpeg_with_correct_extension.jpg';
        $detectionMethod = MimeTypes::DETECT_FROM_CONTENTS | MimeTypes::USE_FILE_EXTENSION;
        self::expectException(ConflictingMimeTypesException::class);
        uopz_set_return('mime_content_type', 'application/javascript');
        $this->mimeTypes->detect($testFile, $detectionMethod);
        uopz_unset_return('mime_content_type');
    }

    /**
     * @return void
     * @throws ConflictingMimeTypesException
     * @throws FileDoesNotExistException
     * @throws FileNotReadableException
     * @throws InvalidMimeDetectionConstantException
     * @throws UnknownMimeTypeDetectedException
     * @covers \pvc\http\mime\MimeTypes::detect
     * @runInSeparateProcess
     */
    public function testDetectThrowsExceptionWithUnknownMimeType(): void
    {
        $testFile = $this->fixturesDirectory . '/' . 'jpeg_with_correct_extension.jpg';
        $detectionMethod = MimeTypes::DETECT_FROM_CONTENTS;
        self::expectException(UnknownMimeTypeDetectedException::class);
        /**
         * the mime types array is mocked, so 'image.tif', although valid in real life, is not 'known' in the mock
         * environment
         */
        uopz_set_return('mime_content_type', 'image/tif');
        $this->mimeTypes->detect($testFile, $detectionMethod);
        uopz_unset_return('mime_content_type');
    }

    /**
     * @return void
     * @throws ConflictingMimeTypesException
     * @throws FileDoesNotExistException
     * @throws FileNotReadableException
     * @throws InvalidMimeDetectionConstantException
     * @covers \pvc\http\mime\MimeTypes::detect
     */
    public function testDetectSucceeds(): void
    {
        $testFile = $this->fixturesDirectory . '/' . 'jpeg_with_correct_extension.jpg';
        $fileExtension = pathinfo($testFile, PATHINFO_EXTENSION);
        $expectedMimeTypeString = $this->mimeTypes->getMimeTypeNameFromFileExtension($fileExtension);

        $detectionMethod = MimeTypes::DETECT_FROM_CONTENTS;
        $actualMimeTypeString = $this->mimeTypes->detect($testFile, $detectionMethod)->getMimeTypeName();
        self::assertEquals($expectedMimeTypeString, $actualMimeTypeString);

        $detectionMethod = MimeTypes::USE_FILE_EXTENSION;
        $actualMimeTypeString = $this->mimeTypes->detect($testFile, $detectionMethod)->getMimeTypeName();
        self::assertEquals($expectedMimeTypeString, $actualMimeTypeString);

        $detectionMethod = MimeTypes::DETECT_FROM_CONTENTS | MimeTypes::USE_FILE_EXTENSION;
        $actualMimeTypeString = $this->mimeTypes->detect($testFile, $detectionMethod)->getMimeTypeName();
        self::assertEquals($expectedMimeTypeString, $actualMimeTypeString);
    }
}
