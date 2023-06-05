<?php

namespace Mpietrucha\Finder\Executable;

use Closure;
use Mpietrucha\Finder\Skeleton;
use Mpietrucha\Support\Argument;
use Mpietrucha\Finder\ExecutableFinder;
use Mpietrucha\Support\Collection as Arguments;
use Mpietrucha\Finder\Contracts\Executable\FindableInterface;

abstract class Handler extends Skeleton implements FindableInterface
{
    protected ?string $input = null;

    protected static mixed $staticInput = null;

    public static function withStaticInput(mixed $input): void
    {
        self::$staticInput = $input;
    }

    public static function input(): mixed
    {
        return self::$staticInput;
    }

    public static function createAsHandler(): self
    {
        $arguments = Argument::arguments(func_get_args())->call(function (Arguments $arguments) {
            $arguments->each->whenInstance(Closure::class, fn (Closure $handler) => new ClosureHandler($handler));
        });

        $handler = self::create(...$arguments);

        ExecutableFinder::handler($handler);

        return $handler;
    }

    public function handling(string $input): void
    {
        $this->input = $input;
    }
}
