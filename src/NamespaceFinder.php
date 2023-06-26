<?php

namespace Mpietrucha\Finder;

use ReflectionException;
use Mpietrucha\Support\Types;
use Mpietrucha\Error\Reporting;
use Mpietrucha\Support\Package;
use Illuminate\Support\Stringable;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Concerns\HasInputFile;
use Mpietrucha\Finder\Contracts\FinderInterface;
use Mpietrucha\Exception\InvalidArgumentException;

class NamespaceFinder extends Skeleton implements FinderInterface
{
    use HasInputFile;

    protected Stringable $contents;

    protected static ?Collection $definitions = null;

    protected const NAMESPACE = ['namespace', ';'];

    protected const INSTANCES = [['trait', 'interface', 'class'], '{'];

    public function __construct(string $contents)
    {
        $this->in($contents);

        parent::__construct();
    }

    public function in(null|string|array $contents): self
    {
        throw_unless(Types::string($contents), new InvalidArgumentException(
            'Argument', ['in'], 'must be string'
        ));

        $this->contents = str($contents);

        return $this;
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
        return Reporting::create()->withoutDeprecated()->withoutRecoverable()->catch()->while(fn () => Package::any($namespace)) === true;
    }

    protected function items(Collection $items): Collection
    {
        return $items->toStringable()->map->trim()->reject->isEmpty()->filter();
    }
}
