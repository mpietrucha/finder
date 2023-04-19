<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Support\Macro;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Finder\ProgressiveFinder;
use Illuminate\Support\Collection;
use Mpietrucha\Finder\Factory\FrameworkFinderFactory;

class Laravel extends FrameworkFinderFactory
{
    protected ?Collection $paths = null;

    protected const FILE = 'artisan';

    protected const IN = '/var/www/html';

    protected const BOOTSTRAP = 'bootstrap/app.php';

    public function name(): string
    {
        return 'laravel';
    }

    public function paths(?string $in = null): Collection
    {
        return $this->paths ??= ProgressiveFinder::create($in ?? self::IN)->files()->name(self::FILE)->find();
    }

    public function boot(string $path): void
    {
        Macro::bootstrap();

        $bootstrap = collect([$path, self::BOOTSTRAP])->toDirectory();

        if (! file_exists($bootstrap)) {
            return;
        }

        $app = require_once $bootstrap;

        $app->boot();
    }
}
