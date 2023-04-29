<?php

namespace Mpietrucha\Finder;

use Illuminate\Support\Collection;
use Symfony\Component\Finder\SplFileInfo;
use Mpietrucha\Finder\Concerns\WithDeepInput;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

class FrameworksFinder extends InstancesFinder
{
    use WithDeepInput;

    protected const DIRECTORY = '/Framework';

    public function __construct()
    {
        parent::__construct(__DIR__.self::DIRECTORY);
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
        return $this->getResultsBuilder()
            ->source($this->namespaces(...))
            ->fresh(function (Collection $namespaces) {
                return $namespaces->map(fn (string $namespace) => $this->getCacheAwareProvider()->put(
                    $namespace::find($this->in), $namespace
                ))
                ->collapse()
                ->tap($this->getCacheAwareProvider()->commit(...));
            })
            ->build();
    }
}
