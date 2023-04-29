<?php

namespace Mpietrucha\Finder;

use Closure;
use Mpietrucha\Support\Macro;
use Mpietrucha\Support\Rescue;
use Mpietrucha\Support\Argument;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\RestorableHigherProxy;
use Mpietrucha\Support\Concerns\ForwardsCalls;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder
{
    use HasFactory;

    use ForwardsCalls;

    protected ?Cache $cache = null;

    protected ?SymfonyFinder $finder = null;

    protected bool $hasCachedResults = false;

    public function __construct(protected string|array $input = [])
    {
        Macro::bootstrap();

        $this->setInput($input)
            ->forwardTo(fn () => $this->setFinder(
                Rescue::create(fn () => SymfonyFinder::create()->ignoreUnreadableDirs()->in($this->input))->call(...)
            ))
            ->forwardFallback()
            ->forwardThenReturnThis()
            ->forwardImmediatelyTapClosure()
            ->forwardFallbackRestore(
                fn (RestorableHigherProxy $proxy) => $proxy->__latest()->__hits()->except('in'),
                fn (?SymfonyFinder $finder) => $this->setFinder($finder)
            )
            ->forwardMethodTap('in', function (string|array $input) {
                $this->notPath($this->input);

                $this->setInput($input);
            })
            ->configure();
    }

    public function configure(): void
    {
    }

    public function setFinder(null|Closure|SymfonyFinder $finder): ?SymfonyFinder
    {
        return $this->finder ??= value($finder);
    }

    public function setInput(string|array $input): self
    {
        $this->input = Argument::arguments($input)->filter->value()->always(function ($argument) {
            return $argument->string();
        })->call();

        return $this;
    }

    public function flatten(): self
    {
        return $this->depth('== 0');
    }

    public function cache(null|array|string $keys = null, mixed $expires = null): self
    {
        $this->cache = Cache::create($keys ?? $this->finder, $expires);

        return $this;
    }

    public function find(): Collection
    {
        return $this->lazy()->collect();
    }

    public function lazy(): LazyCollection
    {
        $this->hasCachedResults = false;

        if ($results = $this->getCacheProvider()?->get()) {
            $this->hasCachedResults = true;

            return $results;
        }

        $iterator = Rescue::create(fn () => $this->finder?->getIterator())->call();

        if (! $iterator) {
            return LazyCollection::empty();
        }

        $results = LazyCollection::make($iterator);

        $this->getCacheProvider()?->put($results);

        return $results;
    }

    public function getCacheProvider(): ?Cache
    {
        return $this->cache;
    }

    public function getCacheAwareProvider(): CacheAware
    {
        return $this->cacheAware ??= CacheAware::create($this->getCacheProvider());
    }

    public function getResultsBuilder(): ResultBuilder
    {
        return ResultBuilder::create($this->find(...), function () {
            return $this->hasCachedResults;
        });
    }
}
