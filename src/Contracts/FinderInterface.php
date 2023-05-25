<?php

namespace Mpietrucha\Finder\Contracts;

use SplFileInfo;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface FinderInterface extends CacheableFinderInterface, FinderAwareInterface
{
    public function first(): null|string|SplFileInfo;

    public function find(): Collection;

    public function lazy(): LazyCollection;
}
