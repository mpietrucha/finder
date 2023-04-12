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
        parent::__construct($in);

        $this->name('*.php');
    }

    public function namespaces(): Collection
    {
        return $this->find()->map(fn (SplFileInfo $file) => Reflector::file($file)->getName());
    }

    public function instances(array $arguments = []): Collection
    {
        return $this->namespaces()->map(fn (string $namespace) => new $namespace(...$arguments));
    }
}
