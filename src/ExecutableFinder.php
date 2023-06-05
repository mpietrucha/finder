<?php

namespace Mpietrucha\Finder;

use Closure;
use SplFileInfo;
use Mpietrucha\Support\Caller;
use Mpietrucha\Finder\Executable;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Finder\Contracts\FinderInterface;
use Mpietrucha\Finder\Contracts\Executable\Inputable;
use Mpietrucha\Finder\Contracts\Executable\Registerable;
use Mpietrucha\Finder\Contracts\Executable\FindableInterface;
use Symfony\Component\Process\ExecutableFinder as SymfonyExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder as SymfonyPhpExecutableFinder;

class ExecutableFinder extends Skeleton implements FinderInterface
{
    protected static ?Collection $handlers = null;

    protected static ?Collection $resolvers = null;

    protected static bool $hasDefaultHandlers = false;

    public function __construct(mixed $default = null, protected Collection $in = new Collection)
    {
        parent::__construct();

        $this->buildDefaultHandlers();

        $this->buildDefaultResolvers();

        $this->in($default);
    }

    public static function extension(string $extension, string $executable): Executable\ExtensionHandler
    {
        return Executable\ExtensionHandler::createAsHandler($extension, $executable);
    }

    public static function contents(string $contents, Closure $handler): Executable\ContentsHandler
    {
        return Executable\ContentsHandler::createAsHandler($contains, $handler);
    }

    public static function handler(Closure|FindableInterface $handler): FindableInterface
    {
        if (! $handler instanceof FindableInterface) {
            $handler = new Executable\ClosureHandler($handler);
        }

        self::handlers()->push($handler);

        return $handler;
    }

    public static function executable(string $executable): ?string
    {
        $executable = str($executable);

        return Caller::create(
            self::resolvers()
                ->filter(fn (Closure $handler, string $resolver) => $executable->is($resolver))
                ->sortKeysDesc()
                ->first()
        )->add(fn (string $executable) => with(new SymfonyExecutableFinder)->find($executable))->call($executable);
    }

    public static function resolver(string $executable, Closure $resolver): void
    {
        if (self::resolvers()->has($executable)) {
            return;
        }

        self::resolvers()->put($executable, $resolver);
    }

    public static function resolvers(): Collection
    {
        return self::$resolvers ??= collect();
    }

    public static function handlers(): Collection
    {
        return self::$handlers ??= collect();
    }

    public function in(null|string|array $in): self
    {
        $this->in->push($in);

        return $this;
    }

    public function first(): ?string
    {
        return $this->find()->first();
    }

    public function find(): Collection
    {
        return $this->lazy()->collect();
    }

    public function lazy(): LazyCollection
    {
        return LazyCollection::make($this->in)
            ->flatten()
            ->filter()
            ->map($this->touch(...))
            ->collapse()
            ->filter()
            ->whenEmpty(fn (LazyCollection $results) => $results->merge($this->in->filter()))
            ->map(self::executable(...))
            ->filter();
    }

    protected function touch(string $input): Collection
    {
        $handlers = self::handlers();

        $instances = $handlers->unique(function (FindableInterface $handler) {
            if ($handler instanceof Inputable) {
                return $handler::class;
            }

            return $handler;
        });

        return $handlers->map(function (FindableInterface $handler) use ($instances, $input) {
            if ($handler === $instances->first()) {
                $instances->shift()->handling($input);
            }

            return $handler->result();
        });
    }

    protected function buildDefaultHandlers(): void
    {
        if (self::$hasDefaultHandlers) {
            return;
        }

        InstanceFinder::create(__DIR__.'/Executable')->namespace(function (string $namespace) {
            return class_implements_interface($namespace, Registerable::class);
        })->instanceable()->each(fn (string $handler) => $handler::register());

        self::$hasDefaultHandlers = true;
    }

    protected function buildDefaultResolvers(): void
    {
        self::resolver('php', function (string $executable) {
            if (! $executable = with(new SymfonyPhpExecutableFinder)->find()) {
                return null;
            }

            return $executable;
        });
    }
}
