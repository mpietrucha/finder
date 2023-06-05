<?php

namespace Mpietrucha\Finder;

use Closure;
use SplFileInfo;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Reflector;
use Illuminate\Support\Collection;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Finder\Contracts\Instance\FinderInterface;

class InstanceFinder extends Finder implements FinderInterface
{
    protected array $callbacks = [];

    protected array $arguments = [];

    protected const CALLBACK_INSTANCE = 'instance';

    protected const CALLBACK_NAMESPACE = 'namespace';

    public function configure(): void
    {
        $this->files()->name('*.php');
    }

    public function namespace(Closure $callback): self
    {
        $this->callbacks[self::CALLBACK_NAMESPACE][] = $callback;

        return $this;
    }

    public function instance(Closure $callback): self
    {
        $this->callbacks[self::CALLBACK_INSTANCE][] = $callback;

        return $this;
    }

    public function arguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function namespaces(): LazyCollection
    {
        return $this->getCacheAdapter()->put(function () {
            return $this->lazy()->map(function (SplFileInfo $file) {
                return NamespaceFinder::file($file)->first();
            })->pipe(function (LazyCollection $results) {
                return $this->withCallbacks($results, self::CALLBACK_NAMESPACE);
            });
        })->as('namespaces')->get();
    }

    public function instanceable(): LazyCollection
    {
        return $this->getCacheAdapter()->put(function () {
            return $this->namespaces()->filter(function (string $namespace) {
                return Reflector::create($namespace)->isInstantiable();
            });
        })->as('instanceable')->get();
    }

    public function instances(): Collection
    {
        $instances = $this->instanceable()->collect()->mapIntoInstance($this->arguments);

        return $this->withCallbacks($instances, self::CALLBACK_INSTANCE);
    }

    protected function withCallbacks(Enumerable $results, string $callback): Enumerable
    {
        return $results->pipeIntoCallback($results->filter(...), Arr::get($this->callbacks, $callback));
    }
}
