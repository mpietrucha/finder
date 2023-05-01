<?php

namespace Mpietrucha\Finder;

use Closure;
use Carbon\Carbon;
use Mpietrucha\Support\Key;
use Mpietrucha\Support\File;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Storage\Adapter;
use Illuminate\Support\Enumerable;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Mpietrucha\Support\Concerns\HasFactory;
use Mpietrucha\Storage\Contracts\ExpiryInterface;
use Mpietrucha\Support\Concerns\InteractsWithTime;

class Cache
{
    use HasFactory;

    use InteractsWithTime;

    protected static ?Adapter $adapter = null;

    protected string $key;

    protected bool $read = true;

    protected bool $write = true;

    protected bool $wasPreviouslyCached = false;

    protected const TIME_POOL = 'finder.cache';

    public function __construct(Finder|string|array $keys, protected mixed $expires)
    {
        $this->key = Key::create($keys)->hash();

        $this->time(self::TIME_POOL)->start();
    }

    public static function adapter(): Adapter
    {
        return self::$adapter ??= Adapter::create()->table(Vendor::create());
    }

    public function read(bool $mode = true): self
    {
        $this->read = $mode;

        return $this;
    }

    public function write(bool $mode = true): self
    {
        $this->write = $mode;

        return $this;
    }

    public function get(): ?LazyCollection
    {
        $this->wasPreviouslyCached = false;

        if (! $this->read) {
            return null;
        }

        $entry = self::adapter()->get($this->key);

        if (! $entry) {
            return null;
        }

        $this->wasPreviouslyCached = true;

        return LazyCollection::make(function () use ($entry) {
            yield from $entry();
        });
    }

    public function put(LazyCollection $results): self
    {
        if ($this->write) {
            $entries = $this->entries($results);

            return $this->force(fn () => $this->results($entries), false);
        }

        return $this;
    }

    public function force(Closure $entry, bool $overrideExpiry = true): self
    {
        $adapter = self::adapter();

        $adapter->expiry(function (ExpiryInterface $expiry) use ($overrideExpiry) {
            $expiry->overrideOnExists($overrideExpiry);

            $expiry->onExpiresResolved($this->incrementExpiryBySearchTime(...));
        });

        $adapter->put($this->key, $entry, $this->expires);

        return $this;
    }

    public function forget(): self
    {
        self::adapter()->forget($this->key);

        return $this;
    }

    public function entries(Enumerable $entries): Collection
    {
        return $entries->map(fn (SplFileInfo $file) => [
             $file->getRealPath(),
             $file->getRelativePath(),
             $file->getRelativePathname()
        ])->collect();
    }

    public function results(Collection $entries): Collection
    {
        return $entries->map(fn (array $file) => File::toSplFileInfo(...$file));
    }

    public function wasPreviouslyCached(): bool
    {
        return $this->wasPreviouslyCached;
    }

    protected function incrementExpiryBySearchTime(Carbon $expires): Carbon
    {
        return $expires->add($this->time()->startInterval());
    }
}
