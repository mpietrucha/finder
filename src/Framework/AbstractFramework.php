<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

abstract class AbstractFramework implements FrameworkFinderInterface
{
    use HasFactory;

    public function __construct(protected string $path)
    {
    }

    public function path(): string
    {
        return $this->path;
    }
}
