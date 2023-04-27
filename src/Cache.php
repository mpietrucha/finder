<?php

namespace Mpietrucha\Finder;

use Mpietrucha\Support\Hash;
use Mpietrucha\Support\File;
use Mpietrucha\Support\Vendor;
use Mpietrucha\Storage\Adapter;
use Mpietrucha\Support\Argument;
use Mpietrucha\Support\Serializer;
use Illuminate\Support\Collection;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Mpietrucha\Support\Concerns\HasFactory;

class Cache
{
    use HasFactory;

    protected string $key;

    protected static ?Adapter $adapter = null;

    public function __construct(Finder|string|array $keys, protected mixed $expires)
    {
        $this->key = Hash::md5(...$this->keys($keys));
    }

    public static function adapter(): Adapter
    {
        return self::$adapter ??= Adapter::create()->table(Vendor::create());
    }

    public function get(): ?Collection
    {
        return self::adapter()->get($this->key)?->values()->map(fn (array $file) => File::toSplFileInfo(...$file));
    }

    public function put(Collection $results): self
    {
        self::adapter()->put($this->key, $results->map(fn (SplFileInfo $file) => [
             $file->getRealPath(),
             $file->getRelativePath(),
             $file->getRelativePathname()
        ]), $this->expires);

        return $this;
    }

    public function forget(): void
    {
        self::adapter()->forget($this->key);
    }

    protected function keys(Finder|string|array $keys): array
    {
        if ($keys instanceof Finder) {
            return [Serializer::create($keys)->serialize()];
        }

        return Argument::arguments($keys)->enshure(fn (Argument $argument) => $argument->nullable()->string());
    }
}
