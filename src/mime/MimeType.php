<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\http\mime;

use pvc\interfaces\http\mimetype\MimeTypeInterface;

/**
 * Class MimeType
 */
class MimeType implements MimeTypeInterface
{
    /**
     * @var string
     */
    protected string $mimeTypeName;

    /**
     * @var array<string>
     */
    protected array $fileExtensions = [];

    /**
     * @inheritDoc
     */
    public function getMimeTypeName(): ?string
    {
        return $this->mimeTypeName ?? null;
    }

    /**
     * @inheritDoc
     */
    public function setMimeTypeName(string $mimeTypeName): void
    {
        $this->mimeTypeName = $mimeTypeName;
    }

    /**
     * @inheritDoc
     */
    public function getFileExtensions(): array
    {
        return $this->fileExtensions;
    }

    /**
     * @inheritDoc
     */
    public function setFileExtensions(array $fileExtensions): void
    {
        $this->fileExtensions = $fileExtensions;
    }
}
