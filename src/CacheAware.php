<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Exception\InvalidArgumentException;
use Closure;
use Mpietrucha\Support\Caller;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Serializer;
use Mpietrucha\Support\Concerns\HasFactory;

class CacheAware
{
    use HasFactory;

    protected ?Collection $results = null;

    public function __construct(protected ?Cache $cache)
    {
    }

    public function put(Collection $entries, Closure|string $builder): Collection
    {
        $builder = Caller::create($builder)->add(fn (Collection $entries) => $this->builder($builder, $entries));

        $this->results()?->push([
            $this->cache->entries($entries),
            $builder->get()
        ]);

        return $builder->call($entries);
    }

    public function commit(): void
    {
        if (! $this->results()?->count()) {
            return;
        }

        $this->cache->force(fn () => $this->restore());
    }

    public function restore(): Collection
    {
        return Serializer::unwrap($this->results)->mapSpread(fn (Collection $files, Closure $builder) => $builder(
            $this->cache->results($files)
        ))->collapse();
    }

    public function builder(string $namespace, Collection $entries): Collection
    {
        throw_unless(resolves_to_object($namespace), new InvalidArgumentException(
            'Given namespace', [$namespace], 'is not resolvable to valid object'
        ));

        return $entries->mapInto($namespace);
    }

    protected function results(): ?Collection
    {
        if (! $this->cache) {
            return null;
        }

        return $this->results ??= collect();
    }
}
