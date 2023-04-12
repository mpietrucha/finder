<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

class FrameworkFinder extends InstancesFinder
{
    public function __construct()
    {
        parent::__construct(__DIR__.'/Framework');
    }

    public function namespaces(): Collection
    {
        return parent::namespaces()->filter(fn (string $namespace) => class_implements_interface($namespace, FrameworkFinderInterface::class));
    }

    public function instances(array $arguments = []): Collection
    {
        return parent::instances($arguments)->filter(fn (FrameworkFinderInterface $framework) => $framework->found());
    }
}
