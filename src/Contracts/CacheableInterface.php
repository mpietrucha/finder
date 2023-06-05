<?php

namespace Mpietrucha\Finder\Contracts;

interface CacheableInterface
{
    public function cache(null|string|array $keys = null, mixed $expires = null): self;

    public function usingCacheAdapter(Cache\AdapterInterface $adapter): self;

    public function getCacheAdapter(): ?Cache\AdapterInterface;
}
