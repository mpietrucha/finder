<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Support\Macro;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Bootstrapper;
use Illuminate\Foundation\Application;
use Mpietrucha\Finder\ProgressiveFinder;
use Mpietrucha\Finder\Factory\FrameworkFinderFactory;

class Laravel extends FrameworkFinderFactory
{
    protected const SEARCH_FILE_NAME = 'artisan';

    protected const DEFAULT_IN = '/ver/www/html';

    protected const BOOTSTRAP_FILE = 'bootstrap/app.php';

    public static function find(?string $in): Collection
    {
        return ProgressiveFinder::create($in ?? self::DEFAULT_IN)
            ->files()
            ->name(self::SEARCH_FILE_NAME)
            ->find()
            ->map->getPath()
            ->map(self::create(...));
    }

    public function name(): string
    {
        return 'laravel';
    }

    public function bootstrapper(): void
    {
        return Bootstrapper::create($this->path, fn (Application $app) => $app->boot());
    }
}
