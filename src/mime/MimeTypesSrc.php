<?php

/**
 * @author: Doug Wilbourne (dougwilbourne@gmail.com)
 */

declare(strict_types=1);

namespace pvc\http\mime;

use pvc\http\err\MimeTypeCdnException;
use pvc\http\err\MimeTypesJsonDecodingException;
use pvc\interfaces\http\mime\MimeTypeFactoryInterface;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;

/**
 * Class MimeTypesSrcMimeDb
 */
class MimeTypesSrc implements MimeTypesSrcInterface
{
    /**
     * @var array<string, object{'source': string, 'extensions': array<string>, 'compressible': bool, 'charset': string}>
     */
    protected array $mimeTypes = [];

    protected MimeTypeFactoryInterface $mimeTypeFactory;

    /**
     * this cdn is a compilation from apache, iana, and nginx.
     * @see https://www.jsdelivr.com/package/npm/mime-db
     */
    protected const CDN = 'https://cdn.jsdelivr.net/gh/jshttp/mime-db@master/db.json';

    public function __construct(MimeTypeFactoryInterface $mimeTypeFactory)
    {
        $this->mimeTypeFactory = $mimeTypeFactory;
    }

    public function initializeMimeTypeData(): void
    {
        if (!$fileContents = file_get_contents(self::CDN)) {
            // @codeCoverageIgnoreStart
            throw new MimeTypeCdnException(self::CDN);
            // @codeCoverageIgnoreEnd
        }

        /**
         * if there was a problem decoding the json, json_decode returns null.
         */

        /** @var null|array<string, object{'source': string, 'extensions': array<string>, 'compressible': bool, 'charset': string}> $array */
        $array = json_decode($fileContents);

        if (is_null($array)) {
            // @codeCoverageIgnoreStart
            throw new MimeTypesJsonDecodingException();
            // @codeCoverageIgnoreEnd
        }

        foreach ($array as $mimeTypeString => $object) {
            $this->mimeTypes[$mimeTypeString] = $object;
        }
    }

    /**
     * getRawMimeTypeData
     * @return array<string, object{'source': string, 'extensions': array<string>, 'compressible': bool, 'charset': string}>
     */
    public function getRawMimeTypeData(): array
    {
        return $this->mimeTypes;
    }

    /**
     * getMimeTypes
     * @return MimeTypeInterface[]
     */
    public function getMimeTypes(): array
    {
        $result = [];
        foreach ($this->mimeTypes as $mimeTypeName => $obj) {
            $mt = $this->mimeTypeFactory->makeMimeType();
            $mt->setMimeTypeName($mimeTypeName);
            /**
             * not all mime types have file extensions defined and if there are none defined, then the stdClass object
             * simply does not have that property.
             */
            $mt->setFileExtensions($obj->extensions ?? []);
            $result[$mimeTypeName] = $mt;
        }
        return $result;
    }
}
