<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare (strict_types=1);

namespace pvcTests\http\mime;

use PHPUnit\Framework\TestCase;
use pvc\http\mime\MimeTypesSrcJsDelivr;
use pvc\interfaces\http\mime\MimeTypeFactoryInterface;
use pvc\interfaces\http\mime\MimeTypeInterface;

class MimeTypesSrcTest extends TestCase
{
    /**
     * @var MimeTypesSrcJsDelivr
     */
    protected MimeTypesSrcJsDelivr $mimeTypesSrc;

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
        $this->mimeTypesSrc = new MimeTypesSrcJsDelivr($this->mimeTypeFactory);
    }

    /**
     * testConstruct
     * @covers \pvc\http\mime\MimeTypesSrcJsDelivr::__construct
     */
    public function testConstruct(): void
    {
        self::assertInstanceOf(MimeTypesSrcJsDelivr::class, $this->mimeTypesSrc);
    }

    /**
     * testGetMimeTypes
     * @throws \pvc\http\err\MimeTypeCdnException
     * @throws \pvc\http\err\MimeTypesJsonDecodingException
     * @covers \pvc\http\mime\MimeTypesSrcJsDelivr::getMimeTypes
     */
    public function testGetMimeTypes(): void
    {
        $mimeTypeMock = $this->createMock(MimeTypeInterface::class);
        $this->mimeTypeFactory->expects($this->any())->method('makeMimeType')->willReturn($mimeTypeMock);
        $mimeTypesArray = $this->mimeTypesSrc->getMimeTypes();
        foreach ($this->testMimeTypes as $mimeType) {
            self::assertInstanceOf(MimeTypeInterface::class, $mimeTypesArray[$mimeType]);
        }
    }
}
