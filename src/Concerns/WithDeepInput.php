<?php

namespace Mpietrucha\Finder\Concerns;

trait WithDeepInput
{
    protected null|array|string $in = null;

    public function in(null|array|string $in): self
    {
        $this->in = $in;

        return $this;
    }
}
