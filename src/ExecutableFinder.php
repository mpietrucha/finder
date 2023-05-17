<?php

namespace Mpietrucha\Finder;

use SplFileInfo;
use Closure;
use Mpietrucha\Finder\Concerns\WithStaticInput;
use Mpietrucha\Finder\Concerns\WithCache;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Macro;
use Mpietrucha\Support\File;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Concerns\HasInputFile;
use Mpietrucha\Finder\Contracts\FinderInterface;
use Mpietrucha\Finder\AbstractExecutableFinder;
use Mpietrucha\Finder\Contracts\ExecutableFinderInterface;

class ExecutableFinder extends AbstractExecutableFinder implements FinderInterface
{
    use HasFactory;

    use HasInputFile;

    use WithCache;

    protected const HANDLERS = [
        Executable\ExtensionHandler::class,
        Executable\ContentsHandler::class
    ];

    public function __construct(mixed $default, protected Collection $input = new Collection)
    {
        $handlers = collect(self::HANDLERS)->mapIntoInstance();

        self::defaultHandlers($handlers);

        $this->add($default);
    }

    public static function extension(string $extension, string $executable): ExtensionHandler
    {
        return Executable\ExtensionHandler::createAsHandler($extension, $executable);
    }

    public static function contents(string $contains, Closure $handler): ContentsHandler
    {
        return Executable\ContentsHandler::createAsHandler($contains, $handler);
    }

    protected static function configure(SplFileInfo $file): ?self
    {
        return self::create($file);
    }

    public function add(mixed $input): self
    {
        $this->input->push($input);

        return $this;
    }

    public function first(): ?string
    {
        return $this->find()->first()?->getPathname();
    }

    public function find(): Collection
    {
        return $this->lazy()->collect();
    }

    public function lazy(): LazyCollection
    {
        if ($results = $this->getCacheProvider()?->get()) {
            return $results;
        }

        $handlers = self::handlers()->reject->shouldRegister();

        $results = LazyCollection::make($this->input)
            ->filter()
            ->map(fn (mixed $input) => $this->touch($handlers, $input)->map(fn (ExecutableFinderInterface $handler) => [
                $handler->result(), $handler->resolveWith()
            ]))
            ->collapse()
            ->mapSpread($this->toResolvedResult(...))
            ->filter();

        $this->getCacheProvider()?->put($results);

        return $results;
    }

    protected function touch(Collection $handlers, mixed $input): Collection
    {
        $instances = $handlers->unique(function (ExecutableFinderInterface $handler) {
            if (class_uses_trait($handler, WithStaticInput::class)) {
                return [WithStaticInput::class, $handler::class];
            }

            return $handler;
        });

        $instances->each(fn (ExecutableFinderInterface $handler) => $handler->handling($input));

        return $handlers;
    }

    protected function toResolvedResult(?string $result, Closure $handler): ?SplFileInfo
    {
        if (! $result) {
            return null;
        }

        return File::toSplFileInfo($handler($result));
    }
}
