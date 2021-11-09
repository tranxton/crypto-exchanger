<?php

declare(strict_types=1);

namespace App\Models\Wallet;

class SystemWallet extends Wallet
{
    /**
     * Возвращает системный кошелек в зависимости от переданной валюты
     *
     * @param int $currency
     *
     * @return Wallet
     */
    public static function getByCurrency(int $currency): Wallet
    {
        return Wallet::where('type_id', Type::SYSTEM)->where('currency_id', $currency)->first();
    }
}
