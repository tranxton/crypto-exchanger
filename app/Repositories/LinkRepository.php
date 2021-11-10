<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Referral\Link;
use Illuminate\Support\Facades\Cache;

class LinkRepository
{
    /**
     * Префикс кэша для пользователя
     *
     * @var string
     */
    protected static string $cache_prefix = 'referral_link_';

    /**
     * Время жизни кэша
     *
     * @var int
     */
    protected static int $cache_ttl = 86400;

    /**
     * Возвращает реферальную ссылку
     *
     * @param string $link
     *
     * @return Link|null
     */
    public static function get(string $link): ?Link
    {
        $cache_key = self::$cache_prefix . $link;
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        $link = Link::where('value', $link)->with('user')->first();
        if ($link === null) {
            return null;
        }

        Cache::put($cache_key, $link, self::$cache_ttl);

        return $link;
    }
}
