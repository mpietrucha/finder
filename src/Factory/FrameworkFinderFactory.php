<?php

namespace Mpietrucha\Finder\Factory;

use Mpietrucha\Support\Vendor;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Finder\Contracts\FrameworkFinderInterface;

abstract class FrameworkFinderFactory implements FrameworkFinderInterface
{
    use HasFactory;

    protected const VENDOR = 'vendor/autoload.php';

    public function __construct(protected string $path)
    {
    }

    public function vendor(): Vendor
    {
        return Vendor::create($this->path);
    }
}
