<?php

namespace Mpietrucha\Finder;

use Closure;
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

class Cache
{
    use HasFactory;

    protected string $key;

    protected bool $read = true;

    protected bool $write = true;

    protected static ?Adapter $adapter = null;

    public function __construct(Finder|string|array $keys, protected mixed $expires)
    {
        $this->key = Key::create($keys)->hash();
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
        if (! $this->read) {
            return null;
        }

        $entry = self::adapter()->get($this->key);

        if (! $entry) {
            return null;
        }

        return LazyCollection::make(function () use ($entry) {
            yield from $entry();
        });
    }

    public function put(LazyCollection $results): self
    {
        if ($this->write) {
            $entries = $this->entries($results);

            self::adapter()->put($this->key, fn () => $this->results($entries), $this->expires);
        }

        return $this;
    }

    public function force(Closure $entry): self
    {
        self::adapter()->put($this->key, $entry, $this->expires);

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

    public function forget(): void
    {
        self::adapter()->forget($this->key);
    }
}
