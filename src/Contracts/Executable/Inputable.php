<?php

namespace Mpietrucha\Finder\Contracts\Executable;

interface Inputable
{
    public static function withStaticInput(mixed $input): void;

    public static function input(): mixed;
}
