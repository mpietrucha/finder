<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Support\Macro;
use Mpietrucha\Error\Reporting;
use Mpietrucha\Support\Package;
use Illuminate\Support\Stringable;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Support\Concerns\HasInputFile;
use Mpietrucha\Finder\Contracts\FinderAwareInterface;

class NamespaceFinder implements FinderAwareInterface
{
    use HasFactory;

    use HasInputFile;

    protected Stringable $contents;

    protected static ?Collection $definitions = null;

    protected const NAMESPACE = ['namespace', ';'];

    protected const INSTANCES = [['trait', 'interface', 'class'], '{'];

    public function __construct(string $contents)
    {
        Macro::bootstrap();

        $this->contents = str($contents);
    }

    public function first(): ?Stringable
    {
        if (! $instance = $this->instance()) {
            return null;
        }

        if (! $namespace = $this->namespace()) {
            return null;
        }

        $namespace = collect([$namespace, $instance->toWordsCollection()->first()])->toNamespace();

        if (! $this->exists($namespace)) {
            return null;
        }

        return $namespace;
    }

    protected function definitions(): Collection
    {
        return self::$definitions ??= collect(self::INSTANCES)->recursive();
    }

    protected function instance(): ?Stringable
    {
        return $this->definitions()->first()->map(function (string $defintion) {
            return $this->contents->toBetweenCollection("$defintion ", $this->definitions()->last())->first();
        })->filter()->pipe($this->items(...))->first();
    }

    protected function namespace(): ?Stringable
    {
        return $this->contents->toBetweenCollection(...self::NAMESPACE)->pipe($this->items(...))->first();
    }

    protected function exists(string $namespace): bool
    {
        return Reporting::create()->disable()->while(function () use ($namespace) {
            return collect(['exists', 'trait', 'interface'])->filter(fn (string $method) => Package::$method($namespace));
        })?->count() === 1;
    }

    protected function items(Collection $items): Collection
    {
        return $items->toStringable()->map->trim()->reject->isEmpty()->filter();
    }
}
