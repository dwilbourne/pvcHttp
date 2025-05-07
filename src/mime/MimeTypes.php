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
    protected array $mimeTypes
        {
            get {
                /** @var array<string, MimeTypeInterface> $mimeTypes */
                $mimeTypes = $this->cache->get($this->cacheKey);
                return $mimeTypes;
            }
        }

    public function __construct(
        ?MimeTypesSrcInterface $src = null,
        ?CacheInterface $cache = null,
        ?int $ttl = null,
    )
    {
        $this->mimeTypesSrc = $src ?: new MimeTypesSrcJsDelivr();

        if ($cache === null) {
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
     * @param string $mimeTypeName
     * @return MimeTypeInterface|null
     */
    public function getMimeType(string $mimeTypeName): ?MimeTypeInterface
    {
        /** @var MimeTypeInterface|null $result */
        $result = $this->mimeTypes[$mimeTypeName] ?? null;
        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getMimeTypeNameFromFileExtension(string $fileExt): ?string
    {
        return array_find_key($this->mimeTypes,
            fn($mimeType) => in_array($fileExt,
                $mimeType->getFileExtensions()));
    }

    /**
     * @inheritDoc
     */
    public function getFileExtensionsFromMimeTypeName(string $mimeTypeName): array
    {
        $mt = $this->mimeTypes[$mimeTypeName] ?? null;
        return $mt ? $mt->getFileExtensions() : [];
    }

    /**
     * @inheritDoc
     */
    public function isValidMimeTypeName(string $mimeTypeName): bool
    {
        return isset($this->mimeTypes[$mimeTypeName]);
    }

    /**
     * { @inheritDoc }
     */
    public function isValidMimeTypeFileExtension(string $fileExt): bool
    {
        return array_any($this->mimeTypes, fn($mimetype) => in_array($fileExt,
            $mimetype->getFileExtensions()));
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
