<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\http\mime;

use pvc\http\err\UnknownMimeTypeDetectedException;
use pvc\http\err\UrlMustBeReadableException;
use pvc\http\Stream;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesCacheInterface;
use pvc\interfaces\http\mime\MimeTypesInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;
use pvc\interfaces\http\UrlInterface;
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
     * @param UrlInterface $url
     * @return MimeTypeInterface
     * @throws UrlMustBeReadableException
     * @throws UnknownMimeTypeDetectedException
     */
    public function detect(UrlInterface $url): MimeTypeInterface
    {
        /**
         * ensure the url is syntactically valid and we can open it for reading
         */
        try {
            $handle = Stream::openForReading($url);
        } catch (Throwable $e) {
            throw new UrlMustBeReadableException($url->render(), $e);
        }
        $detected = mime_content_type($handle) ?: 'unknown';
        Stream::close($handle);

        /**
         * mime_content_type can return false if it is unable to detect the mime type.  Less likely, it could
         * conceivably return a mime type that is unknown in the list of mime types supplied by the cdn that
         * this library is using
         */
        if (!$contentMimeType = $this->getMimeType($detected)) {
            throw new UnknownMimeTypeDetectedException($detected, $url->render());
        }
        return $contentMimeType;
    }
}
