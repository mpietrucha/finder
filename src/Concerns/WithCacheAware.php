<?php

namespace Mpietrucha\Finder\Concerns;

use Exception;
use Mpietrucha\Finder\Contracts\FinderInterface;
use Mpietrucha\Finder\CacheAware;

trait WithCacheAware
{
    protected ?CacheAware $cacheAware = null;

    public function getCacheAwareProvider(): CacheAware
    {
        throw_unless(
            $this instanceof FinderInterface || class_uses_trait($this, WithCache::class),
            new Exception('You have to implement FinderInterface or add WitchCache trait to use Cache aware functionality')
        );

        return $this->cacheAware ??= CacheAware::create($this->getCacheProvider());
    }
}
