<?php

namespace Mpietrucha\Finder\Cache;

use Closure;
use Carbon\Carbon;
use Mpietrucha\Support\Vendor;
use Illuminate\Support\Enumerable;
use Illuminate\Support\LazyCollection;
use Mpietrucha\Storage\Adapter as Storage;
use Mpietrucha\Storage\Contracts\ExpiryInterface;

class StorageAdapter extends Adapter
{
    protected static ?Storage $adapter = null;

    public static function adapter(): Storage
    {
        return self::$adapter ??= Storage::create()->table(Vendor::create());
    }

    public function get(?Closure $after = null): Enumerable
    {
        $adapter = self::adapter()->expiry(function (ExpiryInterface $expiry) {
            $expiry->overrideOnExists($this->override);

            $expiry->onExpiresResolved(fn (Carbon $expires) => $expires->add($this->time()->interval()));
        });

        if ($results = $adapter->get($key = $this->getKey())) {
            return $this->with($after, $results);
        }

        $results = $this->with($this->before, parent::get());

        $adapter->put($key, $results->when($results instanceof LazyCollection, fn (LazyCollection $results) => $results->eager()), $this->expires);

        return $this->with($after, $results);
    }
}
