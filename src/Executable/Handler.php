<?php

namespace Mpietrucha\Finder\Executable;

use Closure;
use Mpietrucha\Finder\Concerns\ResolveWith;
use Mpietrucha\Finder\Contracts\ExecutableFinderInterface;

abstract class Handler implements ExecutableFinderInterface
{
    use ResolveWith;

    protected mixed $input = null;

    public function shouldRegister(): bool
    {
        return false;
    }

    public function register(): void
    {
    }

    public function handling(mixed $input): void
    {
        $this->input = $input;
    }

    public function result(): ?string
    {
        return null;
    }
}
