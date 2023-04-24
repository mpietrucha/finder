<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Support\Macro;
use Mpietrucha\Support\Rescue;
use Mpietrucha\Support\Argument;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Concerns\ForwardsCalls;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder
{
    use HasFactory;

    use ForwardsCalls;

    protected ?Cache $cache = null;

    protected ?SymfonyFinder $finder;

    protected ?Collection $history = null;

    public function __construct(protected string|array $input)
    {
        $this->input = Argument::arguments($input)->filter->value()->always(fn (Argument $argument) => $argument->string())->call();

        $this->forwardTo(
            $this->finder = Rescue::create(fn () => SymfonyFinder::create()->ignoreUnreadableDirs()->in($this->input))->call()
        )->forwardFallback()->forwardsThenReturn(fn (string $method, array $arguments) => $this->withHistory($method, $arguments));

        Macro::bootstrap();

        $this->configure();
    }

    public function configure(): void
    {
    }

    public function flatten(): self
    {
        return $this->depth('== 0');
    }

    public function history(): self
    {
        $this->history ??= collect();

        return $this;
    }

    public function cache(null|array|string $keys = null, mixed $expires = null): self
    {
        $this->cache = Cache::create($keys ?? $this->finder, $expires);

        return $this;
    }

    public function find(): Collection
    {
        if ($results = $this->cache?->get()) {
            return $results;
        }

        $results = Rescue::create(fn () => collect($this->finder))->call(
            collect()
        );

        $this->cache?->put($results);

        return $results;
    }

    protected function withHistory(string $method, array $arguments): self
    {
        $this->history?->list($method, $arguments);

        return $this;
    }

    protected function clone(array $input): self
    {
        $instance = self::create($input);

        $this->history?->each(function (Collection $arguments, string $method) use ($instance) {
            $arguments->each(fn (array $arguments) => $instance->$method(...$arguments));
        });

        return $instance;
    }
}
