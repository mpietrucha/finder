<?php

namespace Mpietrucha\Finder\Contracts;

use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface FinderInterface
{
    public function in(null|string|array $input): self;

    public function first(): mixed;

    public function find(): Collection;

    public function lazy(): LazyCollection;
}
