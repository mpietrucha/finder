<?php

namespace Mpietrucha\Finder\Framework;

use Mpietrucha\Support\Vendor;
use Mpietrucha\Support\Macro;
use Mpietrucha\Finder\Factory\FrameworkFinderFactory;

class Laravel extends FrameworkFinderFactory
{
    public function name(): string
    {
        return 'laravel';
    }

    public function found(): bool
    {
        return function_exists('app_path');
    }

    public function path(): ?string
    {
        return app_path();
    }

    public function vendor(): ?Vendor
    {
        if (! $this->found()) {
            return null;
        }

        return Vendor::create($this->path());
    }

    public function boot(): void
    {
        Macro::bootstrap();

        $bootstrap = collect([
            $this->vendor()->path(),
            'bootstrap/app.php'
        ])->toDirectory();

        if (! file_exists($bootstrap)) {
            return;
        }

        $app = require_once $this->vendor()->path();

        $app->boot();
    }
}
