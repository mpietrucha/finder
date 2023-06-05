<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Finder\Skeleton;
use Mpietrucha\Finder\Contracts\Framework\FindableInterface;

abstract class Framework extends Skeleton implements FindableInterface
{
    public function __construct(protected string $path)
    {
        parent::__construct();
    }

    public function path(): string
    {
        return $this->path;
    }
}
