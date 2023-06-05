<?php

namespace Mpietrucha\Finder\Contracts\Framework;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Bootstrapper;

interface FindableInterface
{
    public static function get(null|array|string $in = null): Collection;

    public function name(): string;

    public function path(): string;

    public function bootstrapper(): Bootstrapper;
}
