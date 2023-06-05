<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Finder\Contracts\Progressive\FinderInterface;

class ProgressiveFinder extends Finder implements FinderInterface
{
    protected string $until = DIRECTORY_SEPARATOR;

    public function until(string $until): self
    {
        $this->until = $until;

        return $this;
    }

    public function lazy(): LazyCollection
    {
        return parent::lazy()->whenEmpty($this->nextTick(...));
    }

    protected function nextTick(): LazyCollection
    {
        return $this->in->filter(fn (string $directory) => $directory !== $this->until)
            ->toStringable()
            ->map(function (Stringable $directory) {
                return $directory->toDirectoryCollection()->withoutLast()->toRootDirectory()->toString();
            })
            ->whenEmpty(fn () => LazyCollection::empty())
            ->whenNotEmpty(function (Collection $in) {
                return $this->notPath($this->in->toArray())->in($in->toArray())->lazy();
            });
    }
}
