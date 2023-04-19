<?php

namespace Mpietrucha\Finder;

use Closure;
use Illuminate\Support\Collection;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

class FrameworksFinder extends InstancesFinder
{
    protected ?string $start = null;

    public function __construct()
    {
        parent::__construct(__DIR__.'/Framework');
    }

    public function in(string $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function namespaces(?Closure $callback = null): Collection
    {
        return parent::namespaces($callback)->filter(fn (string $namespace) => class_implements_interface($namespace, FrameworkFinderInterface::class));
    }

    public function instances(?Closure $callback = null, array $arguments = []): Collection
    {
        return parent::instances($callback, $arguments)->filter(fn (FrameworkFinderInterface $framework) => $framework->paths($this->start)->count());
    }
}
