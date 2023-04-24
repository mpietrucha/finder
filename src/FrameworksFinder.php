<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Mpietrucha\Finder\Concerns\WithDeepInput;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

class FrameworksFinder extends InstancesFinder
{
    use WithDeepInput;

    public function __construct()
    {
        parent::__construct(__DIR__.'/Framework');
    }

    public function configure(): void
    {
        parent::configure();

        $this->flatten()->namespace(function (string $namespace) {
            return class_implements_interface($namespace, FrameworkFinderInterface::class);
        });
    }

    public function instances(): Collection
    {
        return parent::namespaces()->map(fn (string $namespace) => $namespace::find($this->in))->collapse();
    }
}
