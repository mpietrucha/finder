<?php

namespace Mpietrucha\Finder\Concerns;

use Mpietrucha\Finder\Cache;

trait WithCache
{
    protected ?Cache $cache = null;

    public function cache(null|array|string $keys = null, mixed $expires = null): self
    {
        $this->cache = Cache::create($keys ?? $this->getForward(), $expires);

        return $this;
    }

    public function getCacheProvider(): ?Cache
    {
        return $this->cache;
    }
}
