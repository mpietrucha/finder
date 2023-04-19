<?php

namespace Mpietrucha\Finder\Contracts;

use Mpietrucha\Support\Vendor;
use Illuminate\Support\Collection;

interface FrameworkFinderInterface
{
    public static function find(?string $start): Collection;

    public function name(): string;

    public function vendor(): Vendor;

    public function boot(): void;
}
