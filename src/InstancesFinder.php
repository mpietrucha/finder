<?php

namespace Mpietrucha\Finder;

use Closure;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Reflector;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;
use Mpietrucha\Finder\Contracts\InstancesFinderInterface;

class InstancesFinder extends Finder implements InstancesFinderInterface
{
    protected array $callbacks = [];

    protected array $arguments = [];

    protected const CALLBACK_INSTANCE = 'instance';

    protected const CALLBACK_NAMESPACE = 'namespace';

    public function configure(): void
    {
        $this->files()->name('*.php');
    }

    public function namespace(Closure $callback, bool $cached = false): self
    {
        $this->callbacks[self::CALLBACK_NAMESPACE][$cached][] = $callback;

        return $this;
    }

    public function instance(Closure $callback, bool $cached = false): self
    {
        $this->callbacks[self::CALLBACK_INSTANCE][$cached][] = $callback;

        return $this;
    }

    public function arguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function namespaces(): Collection
    {
        return $this->getResultsBuilder()
            ->fresh(function (Collection $namespaces) {
                return $namespaces->map(fn (SplFileInfo $file) => Reflector::file($file)?->getName())->filter();
            })
            ->after(function (Collection $namespaces, bool $cached) {
                return $this->withCallbacks($namespaces, self::CALLBACK_NAMESPACE, $cached);
            })
            ->build();
    }

    public function instanceable(): Collection
    {
        return $this->namespaces()->reject(fn (string $namespace) => Reflector::create($namespace)->isAbstract());
    }

    public function instances(): Collection
    {
        return $this->getResultsBuilder()
            ->source($this->instanceable(...))
            ->fresh(function (Collection $namespaces) {
                return $namespaces->mapIntoInstance($this->arguments);
            })
            ->after(function (Collection $instances, bool $cached) {
                return $this->withCallbacks($instances, self::CALLBACK_INSTANCE, $cached);
            })
            ->build();
    }

    protected function withCallbacks(Collection $results, string $key, bool $cached): Collection
    {
        $callbacks = Arr::get($this->callbacks, collect([$key, (int) $cached])->toDotWord()->toString());

        return $results->pipeIntoCallback($results->filter(...), $callbacks);
    }
}
