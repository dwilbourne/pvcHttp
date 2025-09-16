<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\http\mime;

use Psr\SimpleCache\CacheInterface;
use pvc\http\err\MimeTypesUnreadableStreamException;
use pvc\http\err\UnknownMimeTypeDetectedException;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Psr16Cache;
use Throwable;

/**
 * Class mimetype
 */
class MimeTypes implements MimeTypesInterface
{
    protected MimeTypesSrcInterface $mimeTypesSrc;

    protected CacheInterface $cache;

    protected string $cacheKey = 'mimeTypes';

    /**
     * @var array<string, MimeTypeInterface>
     */
    protected array $mimeTypes;

    public function __construct(
        ?MimeTypesSrcInterface $src = null,
        ?CacheInterface $cache = null,
        ?int $ttl = null,
    )
    {
        $this->mimeTypesSrc = $src ?: new MimeTypesSrcJsDelivr();

        if (!$cache instanceof \Psr\SimpleCache\CacheInterface) {
            $psr6Cache = new FilesystemAdapter();
            $this->cache = new Psr16Cache($psr6Cache);
        } else {
            $this->cache = $cache;
        }

        if ($ttl === null) {
            /**
             * valid for one day
             */
            $ttl = 24 * 60 * 60;
        }
        $this->cache->set($this->cacheKey, $this->mimeTypesSrc->getMimeTypes(),
            $ttl);
    }

    /**
     * @return array<string, MimeTypeInterface>
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMimeTypes(): array
    {
        /** @var array<string, MimeTypeInterface> $result */
        $result = $this->cache->get($this->cacheKey);
        return $result;
    }

    /**
     * @param string $mimeTypeName
     * @return MimeTypeInterface|null
     */
    public function getMimeType(string $mimeTypeName): ?MimeTypeInterface
    {
        $mimeTypes = $this->getMimeTypes();
        return $mimeTypes[$mimeTypeName] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getMimeTypeNameFromFileExtension(string $fileExt): ?string
    {
        foreach ($this->getMimeTypes() as $mimeType) {
            if (in_array($fileExt, $mimeType->getFileExtensions(), true)) {
                return $mimeType->getMimeTypeName();
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getFileExtensionsFromMimeTypeName(string $mimeTypeName): array
    {
        $mimeTypes = $this->getMimeTypes();
        $mt = $mimeTypes[$mimeTypeName] ?? null;
        return $mt ? $mt->getFileExtensions() : [];
    }

    /**
     * @inheritDoc
     */
    public function isValidMimeTypeName(string $mimeTypeName): bool
    {
        $mimeTypes = $this->getMimeTypes();
        return isset($mimeTypes[$mimeTypeName]);
    }

    /**
     * { @inheritDoc }
     */
    public function isValidMimeTypeFileExtension(string $fileExt): bool
    {
        return !is_null($this->getMimeTypeNameFromFileExtension($fileExt));
    }

    /**
     * @param resource $stream
     * @return MimeTypeInterface
     * @throws MimeTypesUnreadableStreamException
     * @throws UnknownMimeTypeDetectedException
     */
    public function detect($stream): MimeTypeInterface
    {
        /**
         * mime_content_type throws a type error if it is not supplied a valid resource
         */
        try {
            $detected = mime_content_type($stream);
        } catch (Throwable $e) {
            throw new MimeTypesUnreadableStreamException($e);
        }

        /**
         * conceivably could return a mime type that is unknown in the list of mime types supplied by the cdn that
         * this library is using
         */
        if ((false === $detected) || !$contentMimeType = $this->getMimeType($detected)) {
            throw new UnknownMimeTypeDetectedException($detected);
        }
        return $contentMimeType;
    }
}
