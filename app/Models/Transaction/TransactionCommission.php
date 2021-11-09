<?php

declare(strict_types=1);

namespace App\Models\Transaction;

use App\Models\Bill\Bill;

class TransactionCommission extends Transaction
{
    /**
     * Создает транзакцию "Комиссия"
     *
     * @param Bill   $bill
     * @param string $value
     *
     * @return Transaction
     */
    public static function create(Bill $bill, string $value): Transaction
    {
        $transfer_data = [
            'bill_id'   => $bill->id,
            'type_id'   => Type::COMMISSION,
            'value'     => $value,
        ];

        return Transaction::create($transfer_data);
    }
}
