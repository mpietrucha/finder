<?php

namespace Mpietrucha\Finder\Contracts;

interface FrameworkFinderInterface
{
    public function name(): string;

    public function paths(?string $in = null)): Collection;

    public function boot(string $path): void;
}
