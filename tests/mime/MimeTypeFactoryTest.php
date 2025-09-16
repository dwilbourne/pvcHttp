<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */
declare (strict_types=1);

namespace pvcTests\http\mime;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversMethod;
use pvc\http\mime\MimeType;
use pvc\http\mime\MimeTypeFactory;

class MimeTypeFactoryTest extends TestCase
{
    protected MimeTypeFactory $mimeTypeFactory;

    public function setUp(): void
    {
        $this->mimeTypeFactory = new MimeTypeFactory();
    }

    /**
     * testMakeMimeType
     * @covers \pvc\http\mime\MimeTypeFactory::makeMimeType
     */
    #[Covers(MimeTypeFactory::class, 'makeMimeType')]
    public function testMakeMimeType(): void
    {
        self::assertInstanceOf(MimeType::class, $this->mimeTypeFactory->makeMimeType());
    }
}
