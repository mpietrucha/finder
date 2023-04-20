<?php

namespace Mpietrucha\Finder\Contracts;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Bootstrapper;

interface FrameworkFinderInterface
{
    public static function find(?string $start): Collection;

    public function name(): string;

    public function path(): string;

    public function bootstrapper(): Bootstrapper;
}
