<?php

namespace Mpietrucha\Finder\Cache;

use Closure;
use Mpietrucha\Finder\Skeleton;
use Illuminate\Support\Enumerable;
use Mpietrucha\Finder\Contracts\Cache\AdapterInterface;

abstract class Adapter extends Skeleton implements AdapterInterface
{
    protected string $key;

    protected mixed $expires;

    protected Closure $results;

    protected ?string $as = null;

    protected bool $override = false;

    protected ?Closure $before = null;

    protected bool $resultsAreCached = false;

    public function readable(): bool
    {
        return true;
    }

    public function key(string $key): self
    {
        $this->key = $key;

        return $this;
    }

    public function expires(mixed $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function put(Closure $results): self
    {
        $this->results = $results;

        $this->time();

        return $this;
    }

    public function before(Closure $before): self
    {
        $this->before = $before;

        return $this;
    }

    public function as(string $key): self
    {
        $this->as = $key;

        return $this;
    }

    public function override(bool $mode = true): self
    {
        $this->override = $mode;

        return $this;
    }

    public function get(?Closure $after = null): Enumerable
    {
        return value($this->results);
    }

    public function getKey(): string
    {
        return collect([$this->key, $this->as])->toDotWord();
    }

    public function resultsAreCached(): bool
    {
        return $this->resultsAreCached;
    }

    protected function with(?Closure $callback, Enumerable $results, bool $resultsAreCached = false): Enumerable
    {
        $this->resultsAreCached = $resultsAreCached;

        return value($callback, $results) ?? $results;
    }
}
