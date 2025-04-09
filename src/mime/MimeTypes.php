<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\http\mime;

use pvc\http\err\MimeTypesUnreadableStreamException;
use pvc\http\err\UnknownMimeTypeDetectedException;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesCacheInterface;
use pvc\interfaces\http\mime\MimeTypesInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;
use Throwable;

/**
 * Class mimetype
 */
class MimeTypes implements MimeTypesInterface
{
    /**
     * @var array <string, MimeTypeInterface>
     */
    protected array $mimetypes;


    /**
     * @param MimeTypesSrcInterface|MimeTypesCacheInterface|null $src
     */
    public function __construct(
        MimeTypesSrcInterface|MimeTypesCacheInterface|null $src = null,
    )
    {
        $src = $src ?: new MimeTypesSrcJsDelivr(new MimeTypeFactory());
        $this->mimetypes = $src->getMimeTypes();
    }

    /**
     * @param string $mimeTypeName
     * @return MimeTypeInterface|null
     */
    public function getMimeType(string $mimeTypeName): ?MimeTypeInterface
    {
        return $this->mimetypes[$mimeTypeName] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getMimeTypeNameFromFileExtension(string $fileExt): ?string
    {
        foreach ($this->mimetypes as $mimeTypeName => $mimeType) {
            if (in_array($fileExt, $mimeType->getFileExtensions())) {
                return $mimeTypeName;
            }
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function getFileExtensionsFromMimeTypeName(string $mimeTypeName): array
    {
        $mt = $this->mimetypes[$mimeTypeName] ?? null;
        return $mt ? $mt->getFileExtensions() : [];
    }

    /**
     * @inheritDoc
     */
    public function isValidMimeTypeName(string $mimeTypeName): bool
    {
        return isset($this->mimetypes[$mimeTypeName]);
    }

    /**
     * { @inheritDoc }
     */
    public function isValidMimeTypeFileExtension(string $fileExt): bool
    {
        foreach ($this->mimetypes as $mimeTypeName => $mimetype) {
            if (in_array($fileExt, $mimetype->getFileExtensions())) {
                return true;
            }
        }
        return false;
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
