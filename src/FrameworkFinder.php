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

    public function instances(): Collection
    {
        return parent::instances()->filter(fn (FrameworkFinderInterface $framework) => $framework->found());
    }
}
