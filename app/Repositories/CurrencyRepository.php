<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Currency;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CurrencyRepository
{
    /**
     * Префикс кэша
     *
     * @var string
     */
    protected static string $cache_prefix = 'currencies';

    /**
     * Время жизни кэша
     *
     * @var int
     */
    protected static int $cache_ttl = 86400;

    /**
     * Возвращает список всех доступных валют
     *
     * @return Collection
     */
    public static function all(): Collection
    {
        $cache_key = self::$cache_prefix;
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        $currency = Currency::all();
        Cache::put($cache_key, $currency, self::$cache_ttl);

        return $currency;
    }

    /**
     * Возвращает валюты по короткому имени
     *
     * @param string $short_name
     *
     * @return Currency
     */
    public static function getByShortName(string $short_name): Currency
    {
        $currencies = self::all();

        return $currencies->where('short_name', $short_name)->first();
    }
}
