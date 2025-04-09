<?php

namespace pvcTests\http;

use PHPUnit\Framework\TestCase;
use pvc\http\err\InvalidResourceException;
use pvc\http\err\InvalidStreamHandleException;
use pvc\http\err\MimeTypesUnreadableStreamException;
use pvc\http\Stream;
use pvc\http\url\Url;
use pvc\interfaces\http\UrlInterface;
use pvcTests\http\fixture\MockFilesysFixture;

class StreamTest extends TestCase
{
    protected MockFilesysFixture $fixture;

    public function setUp(): void
    {
        $this->fixture = new MockFilesysFixture();
    }

    /**
     * @return void
     * @throws MimeTypesUnreadableStreamException
     * @covers \pvc\http\Stream::openForReading
     * @runInSeparateProcess
     */
    public function testOpenForReadingFails(): void
    {
        $url = $this->createMock(Url::class);
        uopz_set_return('fopen', false);
        self::expectException(MimeTypesUnreadableStreamException::class);
        $handle = Stream::openForReading($url);
        unset($handle);
        uopz_unset_return('fopen');
    }

    /**
     * @return void
     * @throws MimeTypesUnreadableStreamException
     * @covers \pvc\http\Stream::openForReading
     */
    public function testOpenForReadingSuccess(): void
    {
        $url = $this->createMock(UrlInterface::class);
        $mockHandle = 'foo';
        $callback = function (string $url) use ($mockHandle) {
            return $mockHandle;
        };
        uopz_set_return('fopen', $callback, true);
        $handle = Stream::openForReading($url);
        self::assertSame($mockHandle, $handle);
        uopz_unset_return('fopen');
    }

    /**
     * @return void
     * @throws InvalidResourceException
     * @throws \pvc\http\err\InvalidStreamHandleException
     * @covers \pvc\http\Stream::close
     */
    public function testCloseFailsIfNotResource(): void
    {
        $badResource = 'foo';
        self::expectException(InvalidResourceException::class);
        Stream::close($badResource);
    }

    /**
     * @return void
     * @throws InvalidResourceException
     * @throws InvalidStreamHandleException
     * @covers \pvc\http\Stream::close
     * @runInSeparateProcess
     */
    public function testClosseFailsIfWrongKindOfResource(): void
    {
        /**
         * wrong kind of resource
         */
        $handle = 'some string';
        uopz_set_return('is_resource', true);
        uopz_set_return('get_resource_type', 'Unknown');
        self::expectException(InvalidStreamHandleException::class);
        Stream::close($handle);
        uopz_unset_return('is_resource');
        uopz_unset_return('get_resource_type');

    }

    /**
     * @return void
     * @throws InvalidResourceException
     * @throws MimeTypesUnreadableStreamException
     * @throws \pvc\http\err\InvalidStreamHandleException
     * @covers \pvc\http\Stream::close
     */
    public function testCloseFailsIfResourceIsAlreadyClosed(): void
    {
        $testFile = $this->fixture->getUrlFile();
        $url = $this->createMock(Url::class);
        $url->method('render')->willReturn($testFile);
        $handle = Stream::openForReading($url);
        Stream::close($handle);
        self::expectException(InvalidResourceException::class);
        Stream::close($handle);
    }
}
