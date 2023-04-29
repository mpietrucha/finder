<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Stringable;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class ProgressiveFinder extends Finder
{
    protected string $stop = DIRECTORY_SEPARATOR;

    public function stop(string $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    public function lazy(): LazyCollection
    {
        return parent::lazy()->whenEmpty($this->nextTick(...));
    }

    protected function nextTick(): LazyCollection
    {
        return collect($this->input)->filter(fn (string $directory) => $directory !== $this->stop)
            ->toStringable()
            ->map(function (Stringable $path) {
                return $path->toDirectoryCollection()->withoutLast()->toRootDirectory();
            })
            ->whenEmpty(fn () => LazyCollection::empty())
            ->whenNotEmpty(function (Collection $input) {
                return $this->forward('in', $input->toArray())->lazy();
            });
    }
}
