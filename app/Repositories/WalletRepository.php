<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Currency;
use App\Models\User\User;
use App\Models\Wallet\Type;
use App\Models\Wallet\Wallet;

class WalletRepository
{
    /**
     * Создает кошелек
     *
     * @param User   $user
     * @param array  $fields
     * @param string $address
     *
     * @return Wallet
     * @throws \Exception
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
            $wallet = Wallet::create($fields);
        } catch (\Exception $e) {
            throw new \Exception('Не удалось создать кошелек', 0 , $e);
        }

        return $wallet;
    }
}
