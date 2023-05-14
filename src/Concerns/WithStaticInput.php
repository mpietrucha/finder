<?php

namespace Mpietrucha\Finder\Concerns;

trait WithStaticInput
{
    protected static mixed $input = null;

    public static function input(): mixed
    {
        return self::$input;
    }

    public static function withStaticInput(mixed $input): void
    {
        self::$input = $input;
    }
}
