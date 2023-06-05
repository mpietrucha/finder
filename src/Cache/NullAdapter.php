<?php

namespace Mpietrucha\Finder\Cache;

class NullAdapter extends Adapter
{
    public function readable(): bool
    {
        return false;
    }
}
