<?php

namespace Mpietrucha\Finder;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Mpietrucha\Support\Types;
use Mpietrucha\Support\Macro;

class ProgressiveFinder extends Finder
{
    protected string $root = DIRECTORY_SEPARATOR;

    public function __construct(protected array|string $in)
    {
        $this->track();

        Macro::bootstrap();

        parent::__construct($in);
    }

    public function root(string $root): self
    {
        $this->root = $root;

        return $this;
    }

    public function find(): Collection
    {
        $results = parent::find();

        if ($results->count()) {
            return $results;
        }

        $in = collect($this->in)->filter(fn (string $in) => $in !== $this->root);

        if (! $in->count()) {
            return collect();
        }

        $instance = self::create(
            $in->toStringable()->map->beforeLast(DIRECTORY_SEPARATOR)->toArray()
        );

        return $this->withHistory($instance)->find();
    }
}
