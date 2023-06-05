<?php

namespace Mpietrucha\Finder\Contracts\Progressive;

interface FinderInterface
{
    public function until(string $until): self;
}
