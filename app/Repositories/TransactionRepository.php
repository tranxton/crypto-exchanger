<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Bill\Bill;
use App\Models\Transaction\Status;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\Type;

class TransactionRepository
{
    public static function createTransfer(Bill $bill, string $value): Transaction
    {
        $transfer_data = [
            'bill_id'   => $bill->id,
            'status_id' => Status::BLOCKED,
            'type_id'   => Type::TRANSFER,
            'value'     => $value,
        ];

        return Transaction::create($transfer_data);
    }

    public static function createCommission(Bill $bill, string $value): Transaction
    {
        $transfer_data = [
            'bill_id'   => $bill->id,
            'status_id' => Status::BLOCKED,
            'type_id'   => Type::COMMISSION,
            'value'     => $value,
        ];

        return Transaction::create($transfer_data);
    }

    public static function createBonus()
    {
    }
}
