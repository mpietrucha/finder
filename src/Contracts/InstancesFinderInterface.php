<?php

namespace Mpietrucha\Finder\Contracts;

use Illuminate\Support\Collection;

interface InstancesFinderInterface
{
  public function instances(): Collection;
}
