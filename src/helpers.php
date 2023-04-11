<?php

use Mpietrucha\Finder\Finder;
use Mpietrucha\Finder\ProgressiveFinder;

if (! function_exists('finder')) {
    function finder(string|array $in): Finder {
        return Finder::create($in);
    }
}

if (! function_exists('progressive_finder')) {
    function progressive_finder(string|array $in): ProgressiveFinder {
        return ProgressiveFinder::create($in);
    }
}
