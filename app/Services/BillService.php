<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\BCMathHelper;
use App\Jobs\Bill\Transfer as BillTransferJob;
use App\Models\Bill\Bill;
use App\Models\Bill\BillTransfer;
use App\Models\Bill\Status;
use App\Models\User\User;
use App\Models\Wallet\Wallet;
use Exception;
use Illuminate\Support\Facades\Cache;

class BillService
{
    use BCMathHelper;

    /**
     * Создание запроса на перевод
     *
     * @param User   $user
     * @param Wallet $sender_wallet
     * @param Wallet $recipient_wallet
     * @param string $value
     *
     * @return Bill
     * @throws Exception
     */
    public static function create(User $user, Wallet $sender_wallet, Wallet $recipient_wallet, string $value): Bill
    {
        if (!$sender_wallet->isUserOwner($user)) {
            throw new Exception('Нельзя совершить перевод с чужого кошелька', 401);
        }

        if ($sender_wallet->currency->id !== $recipient_wallet->currency->id) {
            throw new Exception('Валюты кошельков должны совпадать', 422);
        }

        if ($sender_wallet->address === $recipient_wallet->address) {
            throw new Exception('Кошельки отправителя и получателя должны отличаться', 422);
        }

        if (self::compare($sender_wallet->getBalance(), $value) === -1) {
            throw new Exception('Недостаточно средств для создания перевода', 403);
        }

        return BillTransfer::create($user, $sender_wallet, $recipient_wallet, $value);
    }

    /**
     * Перевод статуса счета в "Принят"
     *
     * @param User $user
     * @param Bill $bill
     *
     * @return Bill
     * @throws Exception
     */
    public static function accept(User $user, Bill $bill): Bill
    {
        if (!$bill->isUserOwner($user)) {
            throw new Exception('Нельзя изменить статус чужого счета', 401);
        }
        if ($bill->status->id !== Status::CREATED) {
            throw new Exception('Нельзя изменить статус счета', 422);
        }
        if (!$bill->accept()) {
            throw new Exception('Не удалось изменить статус счета', 500);
        }
        BillTransferJob::dispatch($bill);

        return $bill;
    }
}
