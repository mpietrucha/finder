<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

class FrameworksFinder extends InstancesFinder
{
    protected null|array|string $in = null;

    public function __construct()
    {
        parent::__construct(__DIR__.'/Framework');
    }

    public function configure(): void
    {
        parent::configure();

        $this->flat()->namespace(function (string $namespace) {
            return class_implements_interface($namespace, FrameworkFinderInterface::class);
        });
    }

    public function in(string|array $in): self
    {
        $this->in = $in;

        return $this;
    }

    public function instances(): Collection
    {
        return parent::namespaces()->map(fn (string $namespace) => $namespace::find($this->in))->collapse();
    }
}
