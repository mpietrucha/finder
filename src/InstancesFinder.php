<?php

namespace Mpietrucha\Finder;

use Closure;
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

    public function namespaces(?Closure $callback = null): Collection
    {
        $namespaces = $this->find()->map(fn (SplFileInfo $file) => Reflector::file($file)->getName());

        return value($callback, $namespaces);
    }

    public function instances(?Closure $callback = null, array $arguments = []): Collection
    {
        return $this->namespaces($callback)->map(fn (string $namespace) => new $namespace(...$arguments));
    }
}
