<?php

namespace Mpietrucha\Finder\Contracts;

use Closure;

interface ExecutableFinderInterface
{
    public function shouldRegister(): bool;

    public function register(): void;

    public function handling(mixed $input): void;

    public function result(): ?string;

    public function resolveWith(): Closure;
}
