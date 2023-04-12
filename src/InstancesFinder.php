<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Mpietrucha\Support\Reflector;
use Symfony\Component\Finder\SplFileInfo;
use Mpietrucha\Finder\Contracts\InstancesFinderInterface;

class InstancesFinder extends Finder implements InstancesFinderInterface
{
    public function __construct(protected array|string $in)
    {
        $this->name('*.php');

        parent::__construct($in);
    }

    public function instances(): Collection
    {
        return $this->find()
            ->map(fn (SplFileInfo $file) => Reflector::file($file)->getName())
            ->map(fn (string $namespace) => new $namespace);
    }
}
