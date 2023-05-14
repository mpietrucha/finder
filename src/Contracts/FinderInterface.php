<?php

namespace Mpietrucha\Finder\Contracts;

use SplFileInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface FinderInterface
{
    public function cache(null|array|string $keys = null, mixed $expires = null): self;

    public function first(): null|string|SplFileInfo;

    public function find(): Collection;

    public function lazy(): LazyCollection;
}
