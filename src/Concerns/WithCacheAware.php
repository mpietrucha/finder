<?php

namespace Mpietrucha\Finder\Concerns;

use mpietrucha\Exception\RuntimeException;
use Mpietrucha\Finder\Contracts\FinderInterface;
use Mpietrucha\Finder\CacheAware;

trait WithCacheAware
{
    protected ?CacheAware $cacheAware = null;

    public function getCacheAwareProvider(): CacheAware
    {
        throw_unless(
            $this instanceof FinderInterface || class_uses_trait($this, WithCache::class),
            new RuntimeException('You have to implement', [FinderInterface::class], 'or', [WithCache::class], 'trait to use cache aware functionality')
        );

        return $this->cacheAware ??= CacheAware::create($this->getCacheProvider());
    }
}
