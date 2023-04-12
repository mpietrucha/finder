<?php

namespace Mpietrucha\Finder\Contracts;

use Illuminate\Support\Collection;

interface InstancesFinderInterface
{
    public function namespaces(): Collection;

    public function instances(array $arguments = []): Collection;
}
