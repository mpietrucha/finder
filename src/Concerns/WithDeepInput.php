<?php

namespace Mpietrucha\Finder\Concerns;

trait WithDeepInput
{
    protected null|array|string $in = null;

    public function in(string|array $in): self
    {
        $this->in = $in;

        return $this;
    }
}
