<?php

namespace Mpietrucha\Finder\Contracts;

use Closure;
use Illuminate\Support\Collection;

interface InstancesFinderInterface
{
    public function namespace(Closure $callback, bool $cached = false): self;

    public function instance(Closure $callback, bool $cached = false): self;

    public function arguments(array $arguments): self;

    public function namespaces(): Collection;

    public function instanceable(): Collection;

    public function instances(): Collection;
}
