<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Support\Macro;
use Illuminate\Support\Collection;
use Mpietrucha\Support\Bootstrapper;
use Illuminate\Foundation\Application;
use Illuminate\Contracts\Console\Kernel;
use Mpietrucha\Finder\ProgressiveFinder;
use Mpietrucha\Finder\Factory\FrameworkFinderFactory;

class Laravel extends FrameworkFinderFactory
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
        return Bootstrapper::create($this->bootstrap(), $this->after(...))->vendor();
    }

    protected function bootstrap(): string
    {
        return collect([$this->path(), self::BOOTSTRAP_FILE])->toDirectory();
    }

    protected function after(Application $app): void
    {
        $app->make(Kernel::class);

        $app->bootstrap();
    }
}
