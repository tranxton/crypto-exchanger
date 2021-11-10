<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Currency;
use App\Models\User\User;
use App\Models\Wallet\Type;
use App\Models\Wallet\Wallet;
use Exception;
use Illuminate\Support\Facades\Cache;

class WalletRepository
{
    /**
     * Префикс кэша для счета
     *
     * @var string
     */
    public static string $cache_prefix = 'wallet_';

    /**
     * Длительность жизни кэша для одного счета
     *
     * @var int
     */
    public static int $cache_ttl = 3600;

    /**
     * Возвращает кошелек по его адресу
     *
     * @param string $address
     *
     * @return Wallet
     */
    public static function getByAddress(string $address): Wallet
    {
        $cache_key = self::$cache_prefix . $address;
        if (Cache::has($cache_key)) {
            return Cache::get($cache_key);
        }

        $wallet = Wallet::where('address', $address)->first();
        self::cacheWallet($wallet);

        return $wallet;
    }


    /**
     * Создает кошелек
     *
     * @param User   $user
     * @param array  $fields
     * @param string $address
     *
     * @return Wallet
     * @throws Exception
     */
    public static function create(User $user, Currency $currency, string $address): Wallet
    {
        $fields = [
            'user_id'     => $user->id,
            'type_id'     => Type::USER,
            'currency_id' => $currency->id,
            'address'     => $address,
            'value'       => Wallet::DEFAULT_VALUE,
        ];

        try {
            /**
             * @var Wallet $wallet
             */
            $wallet = Wallet::create($fields);
        } catch (Exception $e) {
            throw new Exception('Не удалось создать кошелек', 500, $e);
        }
        self::cacheWallet($wallet);

        return $wallet;
    }

    /**
     * Помещает кошелек в кэш
     *
     * @param Wallet $wallet
     *
     * @return bool
     */
    public static function cacheWallet(Wallet $wallet): bool
    {
        $wallet->load('currency');

        return Cache::put(self::$cache_prefix . $wallet->address, $wallet, self::$cache_ttl);
    }
}
