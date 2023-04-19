<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\ForwardsCalls;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Macro;

class Finder
{
    use HasFactory;
    use ForwardsCalls;

    protected SymfonyFinder $finder;

    protected bool $track = false;

    public function __construct(protected string|array $in, protected Collection $history = new Collection)
    {
        $this->forwardTo(
            $this->finder = SymfonyFinder::create()->in($in)
        )->forwardsThenReturn(fn (string $method, array $arguments) => $this->history($method, $arguments));

        Macro::bootstrap();
    }

    public function track(bool $mode = true): self
    {
        $this->track = $mode;

        return $this;
    }

    public function find(): Collection
    {
        return collect($this->finder);
    }

    protected function history(string $method, array $arguments): self
    {
        $this->history->when($this->track, fn (Collection $history) => $history->list($method, $arguments));

        return $this;
    }

    protected function withHistory(self $instance): self
    {
        $this->history->each(function (Collection $arguments, string $method) use ($instance) {
            $arguments->each(fn (array $arguments) => $instance->$method(...$arguments));
        });

        return $instance;
    }
}
