<?php

namespace Mpietrucha\Finder;

use Closure;
use SplFileInfo;
use Mpietrucha\Support\Hash;
use Mpietrucha\Support\File;
use Mpietrucha\Support\Rescue;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Finder\Cache\NullAdapter;
use Mpietrucha\Finder\Cache\StorageAdapter;
use Mpietrucha\Support\Concerns\ForwardsCalls;
use Mpietrucha\Finder\Contracts\FinderInterface;
use Mpietrucha\Finder\Contracts\CacheableInterface;
use Mpietrucha\Finder\Contracts\Cache\AdapterInterface;
use Symfony\Component\Finder\Finder as SymfonyFinder;

class Finder extends Skeleton implements FinderInterface, CacheableInterface
{
    use ForwardsCalls;

    protected bool $bootstrapped = false;

    protected ?Closure $directory = null;

    protected ?AdapterInterface $cache = null;

    public function __construct(null|string|array $default = null, protected Collection $in = new Collection)
    {
        $this->forwardTo(SymfonyFinder::create())->forwardThenReturnThis()->configure();

        $this->in($default)->usingCacheAdapter(new NullAdapter);

        parent::__construct();
    }

    public function configure(): void
    {
    }

    public function directory(Closure $directory): self
    {
        $this->directory = $directory;

        return $this;
    }

    public function in(null|string|array $in): self
    {
        collect($in)->filter()
            ->tap(function (Collection $in) {
                $this->in->push(...$in);
            })
            ->unless(! $this->directory, function (Collection $in) {
                return $in->map($this->directory);
            })
            ->filter()
            ->map(function (string $directory) {
                return Rescue::create(fn () => $this->getForward()->in($directory))->call();
            })
            ->filter()
            ->whenNotEmpty(function () {
                $this->bootstrapped = true;
            });

        return $this;
    }

    public function flatten(): self
    {
        return $this->depth('== 0');
    }

    public function hasDeepInput(): self
    {
        return $this->directory(function (string $directory) {
            if ($this->bootstrapped) {
                return null;
            }

            $this->in->shift();

            return $directory;
        });
    }

    public function first(): ?SplFileInfo
    {
        return $this->find()->first();
    }

    public function find(): Collection
    {
        return $this->lazy()->collect();
    }

    public function lazy(): LazyCollection
    {
        if (! $this->bootstrapped) {
            return LazyCollection::empty();
        }

        return $this->getCacheAdapter()->put(function () {
             return LazyCollection::make(fn () => yield from $this->getForward()->getIterator());
        })->before(fn (LazyCollection $results) => $results->map(fn (SplFileInfo $file) => [
            $file->getRealPath(),
            $file->getRelativePath(),
            $file->getRelativePathname()
        ]))->get(fn (LazyCollection $results) => $results->map(fn (array $file) => File::toSplFileInfo(...$file)));
    }

    public function cache(null|array|string $keys = null, mixed $expires = null): self
    {
        if (! $this->getCacheAdapter()->readable()) {
            $this->usingCacheAdapter(new StorageAdapter);
        }

        $this->getCacheAdapter()->key(
            Hash::md5($keys ?? $this)
        )->expires($expires);

        return $this;
    }

    public function getCacheAdapter(): ?AdapterInterface
    {
        return $this->cache;
    }

    public function usingCacheAdapter(AdapterInterface $cache): self
    {
        $this->cache = $cache;

        return $this;
    }
}
