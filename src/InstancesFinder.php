<?php

namespace Mpietrucha\Finder;

use Closure;
use Mpietrucha\Support\Reflector;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;

class InstancesFinder extends Finder
{
    protected array $arguments = [];

    protected ?Closure $namespace = null;

    public function configure(): void
    {
        $this->files()->name('*.php');
    }

    public function namespace(Closure $callback): self
    {
        $this->namespace = $callback;

        return $this;
    }

    public function arguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function namespaces(): Collection
    {
        $namespaces = $this->find()->map(fn (SplFileInfo $file) => Reflector::file($file)->getName());

        return $namespaces->filter($this->namespace);
    }

    public function instances(): Collection
    {
        return $this->namespaces()->map(fn (string $namespace) => new $namespace(...$this->arguments));
    }
}
