<?php

require 'vendor/autoload.php';

use Mpietrucha\Finder\FrameworksFinder;
use Mpietrucha\Finder\Cache;

use Mpietrucha\Support\Time;
use Mpietrucha\Support\Serializer;

$time = Time::create();

$time->add('xd');

dd(
    Serializer::create($time)->serialize()
);

// Cache::adapter()->delete();

$a = FrameworksFinder::create()->cache('xdxd')->in('/Users/michalpietrucha/Documents/webs')->instances();

dd($a);
