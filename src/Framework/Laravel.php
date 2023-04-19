<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Support\Macro;
use Illuminate\Support\Collection;
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

    public function boot(): void
    {
        Macro::bootstrap();

        $autoload = $this->vendor()->autoload();

        $bootstrap = collect([$this->path, self::BOOTSTRAP_FILE])->toDirectory();

        if (! file_exists($bootstrap) || ! file_exists($autoload)) {
            return;
        }

        require_once $autoload;

        $app = require_once $bootstrap;

        $app->boot();
    }
}
