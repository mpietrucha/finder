<?php

namespace Mpietrucha\Finder\Executable;

use Closure;
use Mpietrucha\Finder\Contracts\ExecutableFinderInterface;

class ClosureHandler extends Handler
{
    public function __construct(protected Closure $handler)
    {
    }

    public function result(mixed ...$arguments): ?string
    {
        $arguments[] = $this->input;

        return value($this->handler, ...$arguments);
    }
}
