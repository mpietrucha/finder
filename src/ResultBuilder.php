<?php

namespace Mpietrucha\Finder;

use Closure;
use Mpietrucha\Support\Types;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;

class ResultBuilder
{
    use HasFactory;

    protected bool $aware = false;

    protected ?Closure $fresh = null;

    protected ?Closure $after = null;

    public function __construct(protected ?Cache $cache, protected Closure|Collection $source = new Collection)
    {
    }

    public function source(Closure|Collection $source): self
    {
        $this->source = $source;

        return $this;
    }

    public function fresh(Closure $fresh): self
    {
        $this->fresh = $fresh;

        return $this;
    }

    public function after(Closure $after): self
    {
        $this->after = $after;

        return $this;
    }

    public function usingCacheAware(bool $mode = true): self
    {
        $this->aware = $mode;

        return $this;
    }

    public function build(): Collection
    {
        return $this->withAware()->pipe($this->withFresh(...))->pipe($this->withAfter(...));
    }

    public function cached(): bool
    {
        return $this->cache?->wasPreviouslyCached() === true;
    }

    public function withAware(): Collection
    {
        if ($this->aware) {
            $this->cache?->write(false);
        }

        return value($this->source);
    }

    public function withFresh(Collection $results): Collection
    {
        if ($this->cached() || ! $this->fresh) {
            return $results;
        }

        return value($this->fresh, $results);
    }

    public function withAfter(Collection $results): Collection
    {
        return (value($this->after, $results, $this->cached()) ?? $results)->tap(function () {
            $this->cache?->write();
        });
    }
}
