<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;

class ProgressiveFinder extends Finder
{
    protected string $stop = DIRECTORY_SEPARATOR;

    public function configure(): void
    {
        $this->track();
    }

    public function stop(string $stop): self
    {
        $this->stop = $stop;

        return $this;
    }

    public function find(): Collection
    {
        return parent::find()->whenEmpty($this->nextTick(...));
    }

    protected function nextTick(): Collection
    {
        return collect($this->in)->filter(fn (string $in) => $in !== $this->stop)
            ->toStringable()
            ->map(function (Stringable $path) {
                return $path->toDirectoryCollection()->withoutLast()->toDirectory();
            })
            ->whenNotEmpty(function (Collection $in) {
                $instance = self::create($in->toArray());

                return $this->withHistory($instance)->find();
            });
    }
}
