<?php

namespace Mpietrucha\Finder\Contracts\Executable;

interface FindableInterface
{
    public function handling(string $input): void;

    public function result(): ?string;
}
