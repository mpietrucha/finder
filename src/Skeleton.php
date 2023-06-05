<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Support\Macro;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Exception\RuntimeException;
use Mpietrucha\Support\Concerns\HasFactory;
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
        throw new RuntimeException('Method', ['find'], 'is not allowed in this finder instance');
    }

    public function lazy(): LazyCollection
    {
        throw new RuntimeException('Method', ['lazy'], 'is not allowed in this finder instance');
    }
}
