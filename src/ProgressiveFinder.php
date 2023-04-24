<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;

class ProgressiveFinder extends Finder
{
    protected bool $stopOnFailure = false;

    protected string $stop = DIRECTORY_SEPARATOR;

    public function configure(): void
    {
        $this->history();
    }

    public function stop(string $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    public function stopOnFailure(): self
    {
        $this->stopOnFailure = true;

        return $this;
    }

    public function find(): Collection
    {
        return parent::find()->whenEmpty($this->nextTick(...));
    }

    protected function nextTick(): Collection
    {
        if (! $this->finder && $this->stopOnFailure) {
            return collect();
        }

        return collect($this->input)->filter(fn (string $directory) => $directory !== $this->stop)
            ->toStringable()
            ->map(function (Stringable $path) {
                return $path->toDirectoryCollection()->withoutLast()->toRootDirectory();
            })
            ->whenNotEmpty(function (Collection $input) {
                return $this->clone($input->toArray())->find();
            });
    }
}
