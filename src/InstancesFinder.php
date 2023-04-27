<?php

namespace Mpietrucha\Finder;

use Closure;
use Illuminate\Support\Arr;
use Mpietrucha\Support\Reflector;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class InstancesFinder extends Finder
{
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

    public function namespaces(): Collection
    {
        $namespaces = $this->find()->map(fn (SplFileInfo $file) => Reflector::file($file)?->getName())->filter();

        return $namespaces->pipeIntoCallback($namespaces->filter(...), Arr::get($this->callbacks, self::CALLBACK_NAMESPACE));
    }

    public function instances(): Collection
    {
        $instances = $this->namespaces()->map(fn (string $namespace) => new $namespace(...$this->arguments));

        return $instances->pipeIntoCallback($instances->filter(...), Arr::get($this->callbacks, self::CALLBACK_INSTANCE));
    }
}
