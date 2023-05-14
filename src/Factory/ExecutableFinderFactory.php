<?php

namespace Mpietrucha\Finder\Factory;

use Closure;
use Mpietrucha\Support\Argument;
use Mpietrucha\Support\Collection as Arguments;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Finder\Executable\ClosureHandler;
use Mpietrucha\Finder\Contracts\ExecutableFinderInterface;

abstract class ExecutableFinderFactory
{
    use HasFactory;

    protected static ?Collection $handlers = null;

    public static function handler(Closure|ExecutableFinderInterface $handler): void
    {
        if (! $handler instanceof ExecutableFinderInterface) {
            self::handler(new ClosureHandler($handler));

            return;
        }

        if ($handler->shouldRegister()) {
            $handler->register();
        }

        self::handlers()->push($handler);
    }

    public static function handlers(): Collection
    {
        return self::$handlers ??= collect();
    }

    public static function defaultHandlers(Collection $handlers): void
    {
        $handlers->each(function (Closure|ExecutableFinderInterface $handler) {
            self::handler($handler);
        });
    }

    public static function createAsHandler(): self
    {
        $arguments = Argument::arguments(func_get_args())->call(function (Arguments $arguments) {
            $arguments->each->whenInstance(Closure::class, fn (Closure $handler) => new ClosureHandler($handler));
        });

        $instance = self::create(...$arguments);

        self::handler($instance);

        return $instance;
    }
}
