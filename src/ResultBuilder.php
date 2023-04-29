<?php

namespace Mpietrucha\Finder;

use Closure;
use Mpietrucha\Support\Types;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;

class ResultBuilder
{
    use HasFactory;

    protected ?Closure $fresh = null;

    protected ?Closure $after = null;

    public function __construct(protected Closure|Collection $source = new Collection, protected Closure|bool $cached = false)
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

    public function build(): Collection
    {
        return value($this->source)->pipe($this->withFresh(...))->pipe($this->withAfter(...));
    }

    public function cached(): bool
    {
        return value($this->cached);
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
        return value($this->after, $results, $this->cached()) ?? $results;
    }
}
