<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Support\Macro;
use Mpietrucha\Support\File;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Bootstrapper;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Mpietrucha\Finder\ProgressiveFinder;

class Laravel extends AbstractFramework
{
    protected const SEARCH_FILE_NAME = 'artisan';

    protected const DEFAULT_IN = '/var/www/html';

    protected const BOOTSTRAP_FILE = 'bootstrap/app.php';

    public static function find(null|array|string $in): Collection
    {
        return ProgressiveFinder::create($in ?? self::DEFAULT_IN)
            ->files()
            ->name(self::SEARCH_FILE_NAME)
            ->find();
    }

    public function name(): string
    {
        return 'laravel';
    }

    public function bootstrapper(): Bootstrapper
    {
        return Bootstrapper::create($this->bootstrap(), function (Application $app) {
            $kernel = $app->make(Kernel::class);

            $kernel->bootstrap();
        })->vendor();
    }

    protected function bootstrap(): string
    {
        return collect([File::dirname($this->path()), self::BOOTSTRAP_FILE])->toDirectory();
    }
}
