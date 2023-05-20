<?php

namespace Mpietrucha\Finder;

use Closure;
use SplFileInfo;
use Mpietrucha\Support\Macro;
use Mpietrucha\Support\Rescue;
use Mpietrucha\Support\Argument;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\RestorableHigherProxy;
use Mpietrucha\Support\Concerns\ForwardsCalls;
use Symfony\Component\Finder\Finder as SymfonyFinder;
use Mpietrucha\Finder\Contracts\FinderInterface;
use Mpietrucha\Finder\Concerns\WithCache;

class Finder implements FinderInterface
{
    use HasFactory;

    use ForwardsCalls;

    use WithCache;

    public function __construct(protected string|array $input = [])
    {
        Macro::bootstrap();

        $this->setInput($input);

        $this->setFinder(fn () => Rescue::create(fn () => SymfonyFinder::create()->ignoreUnreadableDirs()->in($this->input))->call(...))
            ->forwardFallback()
            ->forwardThenReturnThis()
            ->forwardImmediatelyTapClosure()
            ->forwardFallbackRestore(
                fn (RestorableHigherProxy $proxy) => $proxy->__latest()->__hits()->except('in'),
                fn (?SymfonyFinder $finder) => $this->setFinder($finder)
            )
            ->forwardMethodTap('in', function (string|array $input) {
                $this->notPath($this->input)->setInput($input);
            })
            ->configure();
    }

    public function configure(): void
    {
    }

    public function setFinder(null|Closure|SymfonyFinder $finder): self
    {
        return $this->forwardTo($finder);
    }

    public function setInput(string|array $input): array
    {
        return $this->input = Argument::arguments($input)->filter->value()->always(function ($argument) {
            return $argument->string();
        })->call();
    }

    public function flatten(): self
    {
        return $this->depth('== 0');
    }

    public function first(): SplFileInfo
    {
        return $this->find()->first();
    }

    public function find(): Collection
    {
        return $this->lazy()->collect();
    }

    public function lazy(): LazyCollection
    {
        if ($results = $this->getCacheProvider()?->get()) {
            return $results;
        }

        $iterator = Rescue::create(fn () => $this->getForward()?->getIterator())->call();

        if (! $iterator) {
            return LazyCollection::empty();
        }

        $results = LazyCollection::make($iterator);

        $this->getCacheProvider()?->put($results);

        return $results;
    }

    public function getResultsBuilder(): ResultBuilder
    {
        return ResultBuilder::create($this->getCacheProvider())->source($this->find(...));
    }
}
