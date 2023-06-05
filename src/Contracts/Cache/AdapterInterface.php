<?php

namespace Mpietrucha\Finder\Contracts\Cache;

use Closure;
use Illuminate\Support\Enumerable;

interface AdapterInterface
{
    public function readable(): bool;

    public function key(string $key): self;

    public function expires(mixed $expires): self;

    public function put(Closure $results): self;

    public function before(Closure $before): self;

    public function as(string $key): self;

    public function override(bool $mode = true): self;

    public function get(?Closure $after = null): Enumerable;
}
