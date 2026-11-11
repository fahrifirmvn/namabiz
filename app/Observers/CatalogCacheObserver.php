<?php

namespace App\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class CatalogCacheObserver
{
    public const FILTER_OPTIONS_KEY = 'discover.filter_options.v2';

    public const RECOMMENDED_KEY = 'discover.recommended_ids';

    public function saved(Model $model): void
    {
        self::flush();
    }

    public function deleted(Model $model): void
    {
        self::flush();
    }

    public static function flush(): void
    {
        Cache::forget(self::FILTER_OPTIONS_KEY);
        Cache::forget(self::RECOMMENDED_KEY);
    }
}
