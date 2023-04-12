<?php

require 'vendor/autoload.php';

use Mpietrucha\Finder\ProgressiveFinder;

dd(
    ProgressiveFinder::create(__DIR__)->name('xd.json')->find()->first()
);
