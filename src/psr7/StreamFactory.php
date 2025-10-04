<?php

namespace pvc\http\psr7;

use GuzzleHttp\Psr7\Stream as GuzzleStream;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use pvc\http\err\OpenFileException;

class StreamFactory implements StreamFactoryInterface
{

    public function createStream(string $content = ''): StreamInterface
    {
        $stream = fopen('php://memory', 'r+');
        assert(false !== $stream);
        fwrite($stream, $content);
        rewind($stream);
        return new GuzzleStream($stream);
    }

    public function createStreamFromFile(
        string $filename,
        string $mode = 'r'
    ): StreamInterface {
        if (false === ($fp = @fopen($filename, $mode))) {
            throw new OpenFileException($filename, $mode);
        }
        return new GuzzleStream($fp);
    }

    public function createStreamFromResource($resource): StreamInterface
    {
        return new GuzzleStream($resource);
    }
}