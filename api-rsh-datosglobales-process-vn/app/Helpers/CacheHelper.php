<?php
namespace App\Helpers;

use Illuminate\Support\Facades\Cache;

class CacheHelper
{
    public static function rememberSafe(
        string $key,
        int $ttl,
        callable $callback,
        bool $useCache = true,
        bool $useLock = true
    ) {
        if (!$useCache) {
            return $callback();
        }

        $store = config('cache.default');
        $canUseLock = $useLock && in_array($store, ['redis', 'memcached']);

        if ($canUseLock) {
            try {
                return Cache::lock('lock:' . $key, 10)->block(5, function () use ($key, $ttl, $callback) {

                    $cached = Cache::get($key);
                    if ($cached !== null) {
                        return $cached;
                    }

                    $data = $callback();

                    if (is_array($data) &&
                        ($data['_no_cache'] ?? false)
                    ) {
                        return $data['data'];
                    }

                    if (empty($data)) {
                        return $data;
                    }

                    Cache::store('redis')->put(
                        $key,
                        $data,
                        now()->addSeconds($ttl)
                    );

                    return $data;
                });
            } catch (\Throwable $e) {
                return self::executeWithoutLock($key, $ttl, $callback);
            }
        }
        return self::executeWithoutLock($key, $ttl, $callback);
    }

    private static function executeWithoutLock($key, $ttl, $callback)
    {
        $cached = Cache::get($key);

        if ($cached !== null) {
            return $cached;
        }

        $data = $callback();
        if (is_array($data) &&
            ($data['_no_cache'] ?? false)
        ) {
            return $data['data'];
        } else {
            if (!empty($data)) {
                Cache::put($key, $data, now()->addSeconds($ttl));
            }
        }

        return $data;
    }
}
