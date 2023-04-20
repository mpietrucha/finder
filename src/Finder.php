<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Support\Macro;
use Mpietrucha\Support\Rescue;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;
use Symfony\Component\Finder\Finder as Base;
use Mpietrucha\Support\Concerns\ForwardsCalls;

class Finder
{
    use HasFactory;
    use ForwardsCalls;

    protected Base $finder;

    protected bool $track = false;

    public function __construct(protected string|array $in, protected Collection $history = new Collection)
    {
        $this->forwardTo(
            $this->finder = Rescue::create(fn () => Base::create()->in($in))->fallback()->call()
        )->forwardsThenReturn(fn (string $method, array $arguments) => $this->history($method, $arguments));

        Macro::bootstrap();

        $this->configure();
    }

    public function configure(): void
    {
    }

    public function flat(): self
    {
        return $this->depth('== 0');
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
