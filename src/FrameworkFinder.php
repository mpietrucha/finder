<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Reflector;
use Symfony\Component\Finder\SplFileInfo;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

class FrameworkFinder extends Finder
{
    public function __construct()
    {
        parent::__construct(__DIR__.'/Framework');

        $this->name('*.php');
    }

    public function find(): Collection
    {
        return parent::find()
            ->map(fn (SplFileInfo $file) => Reflector::file($file)->getName())
            ->map(fn (string $namespace) => new $namespace)
            ->filter(fn (FrameworkFinderInterface $framework) => $framework->found());
    }
}
