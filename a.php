<?php

require 'vendor/autoload.php';

use Mpietrucha\Finder\Finder;
use Mpietrucha\Finder\Cache;
use Carbon\Carbon;

dd(
    Cache::adapter()->delete()
);

die;

$finder = Finder::create(__DIR__ . '/../');

dd(
    $finder->files()->cache('xd', Carbon::now())->name('*.php')->find()
);
