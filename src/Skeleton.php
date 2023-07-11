<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Support\Macro;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Exception\BadMethodCallException;
use Mpietrucha\Support\Concerns\InteractsWithTime;

abstract class Skeleton
{
    use HasFactory;

    use InteractsWithTime;

    public function __construct()
    {
        Macro::bootstrap();
    }

    public function find(): Collection
    {
        throw new BadMethodCallException('Method', ['find'], 'is not allowed in this finder instance');
    }

    public function lazy(): LazyCollection
    {
        throw new BadMethodCallException('Method', ['lazy'], 'is not allowed in this finder instance');
    }
}
