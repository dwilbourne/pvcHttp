<?php

namespace pvcTests\http\mime;

use PHPUnit\Framework\TestCase;
use Psr\SimpleCache\CacheInterface;
use pvc\http\mime\MimeTypesCache;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;

class MimeTypesCacheTest extends TestCase
{
    protected CacheInterface $psrCache;
    protected MimeTypesCache $mimeTypesCache;
    protected MimeTypesSrcInterface $mimeTypesSrc;

    public function setUp(): void
    {
        $this->psrCache = $this->createMock(CacheInterface::class);
        $this->mimeTypesSrc = $this->createMock(MimeTypesSrcInterface::class);
        $this->mimeTypesCache = new MimeTypesCache($this->psrCache, $this->mimeTypesSrc);
    }

    /**
     * @return void
     * @covers \pvc\http\mime\MimeTypesCache::__construct
     * @covers \pvc\http\mime\MimeTypesCache::getMimeTypes
     */
    public function testGetMimeTypesWhenCacheIsEmpty(): void
    {
        $this->psrCache->expects($this->once())->method('has');
        $this->psrCache->expects($this->once())->method('set');
        $this->mimeTypesSrc->expects($this->once())->method('getMimeTypes');
        $mockArrayResult = [];
        $this->psrCache->expects($this->once())->method('get')->willReturn($mockArrayResult);
        $mimeTypes = $this->mimeTypesCache->getMimeTypes();
        self::assertEquals($mockArrayResult, $mimeTypes);
    }
}
