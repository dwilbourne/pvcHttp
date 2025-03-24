<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\http\mime;

use pvc\err\pvc\file\FileDoesNotExistException;
use pvc\err\pvc\file\FileNotReadableException;
use pvc\http\err\ConflictingMimeTypesException;
use pvc\http\err\InvalidMimeDetectionConstantException;
use pvc\http\err\UnknownMimeTypeDetectedException;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;

/**
 * Class mimetype
 */
class MimeTypes implements MimeTypesInterface
{
    public const DETECT_FROM_CONTENTS = 1;
    public const USE_FILE_EXTENSION = 2;

    /**
     * @var array <string, MimeTypeInterface>
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

    private function validateMimeTypeDetectionMethods(int $detectionMethods): bool
    {
        return (($detectionMethods & self::DETECT_FROM_CONTENTS) || ($detectionMethods & self::USE_FILE_EXTENSION));
    }

    public function detect(string $filePath, int $detectionMethods): MimeTypeInterface
    {
        if (!file_exists($filePath)) {
            throw new FileDoesNotExistException($filePath);
        }
        if (!is_readable($filePath)) {
            throw new FileNotReadableException($filePath);
        }
        if (!$this->validateMimeTypeDetectionMethods($detectionMethods)) {
            throw new InvalidMimeDetectionConstantException();
        }
        if ($detectionMethods & self::DETECT_FROM_CONTENTS) {
            /**
             * mime_content_type can return false if it is unable to detect the mime type.  Less likely, it could
             * conceivably return a mime type that is unknown in the list of mime types supplied by the cdn that
             * this library is using
             */
            $detected = mime_content_type($filePath) ?: '';
            if (!$contentMimeType = $this->getMimeType($detected)) {
                throw new UnknownMimeTypeDetectedException($detected, $filePath);
            }
        } else {
            $contentMimeType = null;
        }

        if ($detectionMethods & self::USE_FILE_EXTENSION) {
            $mimeTypeName = $this->getMimeTypeNameFromFileExtension(pathinfo($filePath, PATHINFO_EXTENSION)) ?: '';
            $fileExtensionMimeType = $this->getMimeType($mimeTypeName);
        } else {
            $fileExtensionMimeType = null;
        }

        if (($contentMimeType && $fileExtensionMimeType) && $contentMimeType !== $fileExtensionMimeType) {
            throw new ConflictingMimeTypesException($filePath);
        }

        $result = $contentMimeType ?: $fileExtensionMimeType;
        assert($result instanceof MimeTypeInterface);
        return $result;
    }


}