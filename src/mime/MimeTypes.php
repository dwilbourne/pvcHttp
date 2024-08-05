<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\http\mime;

use pvc\interfaces\http\mimetype\MimeTypesInterface;
use pvc\interfaces\http\mimetype\MimeTypesSrcInterface;

/**
 * Class mimetype
 */
class MimeTypes implements MimeTypesInterface
{
    /**
     * @var array <string, \pvc\interfaces\http\mimetype\MimeTypeInterface>
     */
    protected array $mimetypes;

    /**
     * @param MimeTypesSrcInterface $mimeTypesSrc
     */
    public function __construct(MimeTypesSrcInterface $mimeTypesSrc)
    {
        $mimeTypesSrc->initializeMimeTypeData();
        $this->mimetypes = $mimeTypesSrc->getMimeTypes();
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
}