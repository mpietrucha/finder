<?php

namespace Mpietrucha\Finder;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Mpietrucha\Support\Types;

class ProgressiveFinder extends Finder
{
    protected string $root = DIRECTORY_SEPARATOR;

    public function __construct(protected array|string $in)
    {
        $this->track();

        parent::__construct($in);
    }

    public function root(string $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function find(): Collection
    {
        return parent::find()->whenEmpty($this->nextTick(...));
    }

    protected function nextTick(): Collection
    {
        return collect($this->in)->filter(fn (string $in) => $in !== $this->root)
            ->toStringable()
            ->map->beforeLast(DIRECTORY_SEPARATOR)
            ->whenNotEmpty(function (Collection $in) {
                $instance = self::create($in->toArray());

                return $this->withHistory($instance)->find();
            });
    }
}
