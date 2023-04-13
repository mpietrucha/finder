<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Support\Macro;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Finder\ProgressiveFinder;
use Illuminate\Support\Collection;
use Mpietrucha\Finder\Factory\FrameworkFinderFactory;

class Laravel extends FrameworkFinderFactory
{
    protected ?Collection $bootstrap = null;

    protected const FILE = 'artisan';

    protected const ROOT = '/var/www/html';

    protected const BOOTSTRAP = 'bootstrap/app.php';

    public function name(): string
    {
        return 'laravel';
    }

    public function found(): bool
    {
        return $this->lookup()->count();
    }

    public function path(): ?string
    {
        return $this->lookup()->first()?->getPath();
    }

    public function vendor(): ?Vendor
    {
        if (! $this->path()) {
            return null;
        }

        return Vendor::create($this->path());
    }

    public function boot(): void
    {
        Macro::bootstrap();

        $bootstrap = collect([$this->path(), self::BOOTSTRAP])->toDirectory();

        if (! file_exists($bootstrap)) {
            return;
        }

        $app = require_once $bootstrap;

        $app->boot();
    }

    protected function lookup(): Collection
    {
        return $this->bootstrap ??= ProgressiveFinder::create(self::ROOT)->name(self::FILE)->find();
    }
}
