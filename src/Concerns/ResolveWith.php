<?php

namespace Mpietrucha\Finder\Concerns;

use Closure;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\PhpExecutableFinder;

trait ResolveWith
{
    protected ?Closure $resolver = null;

    public function resolveWith(): Closure
    {
        if (! $this->resolver) {
            $this->resolveWithSymfonyExecutableFinder();
        }

        return $this->resolver;
    }

    public function setResolver(Closure $resolver): void
    {
        $this->resolver = $resolver;
    }

    public function resolveWithSymfonyExecutableFinder(): void
    {
        $this->setResolver(function (string $executable) {
            return with(new ExecutableFinder)->find($executable);
        });
    }

    public function resolveWithSymfonyPhpExecutableFinder(): void
    {
        $this->setResolver(function (string $executable) {
            return with(new PhpExecutableFinder)->find();
        });
    }
}
