<?php

namespace Mpietrucha\Finder\Contracts\Instance;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface FinderInterface
{
    public function namespace(Closure $callback): self;

    public function instance(Closure $callback): self;

    public function arguments(array $arguments): self;

    public function namespaces(): LazyCollection;

    public function instanceable(): LazyCollection;

    public function instances(): Collection;
}
