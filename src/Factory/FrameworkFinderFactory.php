<?php

namespace Mpietrucha\Finder\Factory;

use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

abstract class FrameworkFinderFactory implements FrameworkFinderInterface
{
    use HasFactory;

    public function __construct(protected string $path)
    {
    }
}
