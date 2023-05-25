<?php

namespace Mpietrucha\Finder\Contracts;

interface CacheableFinderInterface
{
    public function cache(null|array|string $keys = null, mixed $expires = null): self;
}
