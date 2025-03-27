<?php

namespace pvc\http\mime;

use DateInterval;
use Psr\SimpleCache\CacheInterface;
use pvc\interfaces\http\mime\MimeTypeInterface;
use pvc\interfaces\http\mime\MimeTypesCacheInterface;
use pvc\interfaces\http\mime\MimeTypesSrcInterface;

class MimeTypesCache implements MimeTypesCacheInterface
{
    protected string $cacheKey = 'mimeTypes';

    public function __construct(
        protected CacheInterface        $cache,
        protected MimeTypesSrcInterface $mimeTypesSrc,
        /**
         * @var DateInterval|int|null time to live before the cache goes stale
         */
        protected DateInterval|int|null $ttl = null,
    )
    {
    }

    /**
     * @return array<string, MimeTypeInterface>
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getMimeTypes(): array
    {
        if (!$this->cache->has($this->cacheKey)) {
            $this->cache->set($this->cacheKey, $this->mimeTypesSrc->getMimeTypes(), $this->ttl);
        }
        /** @var array<string, MimeTypeInterface> $result */
        $result = $this->cache->get($this->cacheKey);
        return $result;
    }
}