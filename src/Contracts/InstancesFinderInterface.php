<?php

namespace Mpietrucha\Finder\Contracts;

use Closure;
use Illuminate\Support\Collection;

interface InstancesFinderInterface
{
    public function namespaces(?Closure $callback): Collection;

    public function instances(?Closure $callback = null, array $arguments = []): Collection;
}
