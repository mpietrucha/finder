<?php

namespace Mpietrucha\Finder\Contracts;

use Mpietrucha\Support\Vendor;

interface FrameworkFinderInterface
{
    public function found(): bool;

    public function path(): ?string;

    public function vendor(): ?Vendor;
}
