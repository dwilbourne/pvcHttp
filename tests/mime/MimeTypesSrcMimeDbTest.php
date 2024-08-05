<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\http\mime;

use pvc\http\mime\MimeTypesSrcMimeDb;
use PHPUnit\Framework\TestCase;
use pvc\interfaces\http\mimetype\MimeTypeFactoryInterface;
use pvc\interfaces\http\mimetype\MimeTypeInterface;

class MimeTypesSrcMimeDbTest extends TestCase
{
    /**
     * @var MimeTypesSrcMimeDb
     */
    protected MimeTypesSrcMimeDb $mimeDb;

    protected MimeTypeFactoryInterface $mimeTypeFactory;

    /**
     * @var array<string>
     */
    protected array $testMimeTypes = [
        'application/javascript',
        'image/jpeg',
        'text/plain',
    ];

    public function setUp(): void
    {
        $this->mimeTypeFactory = $this->createMock(MimeTypeFactoryInterface::class);
        $this->mimeDb = new MimeTypesSrcMimeDb($this->mimeTypeFactory);
    }

    /**
     * testConstruct
     * @covers \pvc\http\mime\MimeTypesSrcMimeDb::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(MimeTypesSrcMimeDb::class, $this->mimeDb);
    }

    /**
     * testGetMimeTypeData
     * @throws \pvc\http\err\MimeTypeCdnException
     * @throws \pvc\http\err\MimeTypesJsonDecodingException
     */
    public function testInitializeMimeTypeData(): void
    {
        $this->mimeDb->initializeMimeTypeData();

        /**
         * verify that the array is not empty
         */
        self::assertNotEmpty($this->mimeDb->getRawMimeTypeData());

        /**
         * illustrate that the keys in the raw data are mime types and the values are objects
         */
        $rawData = $this->mimeDb->getRawMimeTypeData();
        foreach ($this->testMimeTypes as $mimeType) {
            self::assertIsObject($rawData[$mimeType]);
        }
    }

    /**
     * testGetMimeTypes
     * @throws \pvc\http\err\MimeTypeCdnException
     * @throws \pvc\http\err\MimeTypesJsonDecodingException
     * @covers \pvc\http\mime\MimeTypesSrcMimeDb::getMimeTypes
     */
    public function testGetMimeTypes(): void
    {
        $this->mimeDb->initializeMimeTypeData();
        $mimeTypeMock = $this->createMock(MimeTypeInterface::class);
        $this->mimeTypeFactory->expects($this->any())->method('makeMimeType')->willReturn($mimeTypeMock);
        $mimeTypesArray = $this->mimeDb->getMimeTypes();
        foreach ($this->testMimeTypes as $mimeType) {
            self::assertInstanceOf(MimeTypeInterface::class, $mimeTypesArray[$mimeType]);
        }
    }
}
