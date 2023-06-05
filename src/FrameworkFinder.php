<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Mpietrucha\Finder\Contracts\Framework\FindableInterface;

class FrameworkFinder extends InstanceFinder
{
    public function __construct(null|string|array $in = null)
    {
        parent::__construct(__DIR__.'/Framework');

        $this->in($in);
    }

    public function configure(): void
    {
        parent::configure();

        $this->hasDeepInput()->flatten()->namespace(function (string $namespace) {
            return class_implements_interface($namespace, FindableInterface::class);
        });
    }

    public function instances(): Collection
    {
        return $this->getCacheAdapter()->put(function () {
            return $this->instanceable()->collect()->map(function (string $namespace) {
                return $namespace::get($this->in->toArray())->mapInto($namespace);
            })->collapse();
        })->as('frameworks')->get();
    }
}
