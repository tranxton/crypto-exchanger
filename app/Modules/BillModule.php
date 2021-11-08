<?php

declare(strict_types=1);

namespace App\Modules;

use App\Helpers\BCMathHelper;
use App\Models\Bill\Bill;
use App\Models\User\User;
use App\Models\Wallet\Wallet;
use App\Repositories\BillRepository;
use Exception;

class BillModule
{
    use BCMathHelper;

    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param Wallet $sender_wallet
     * @param Wallet $recipient_wallet
     * @param string $value
     *
     * @return Bill
     * @throws Exception
     */
    public function create(Wallet $sender_wallet, Wallet $recipient_wallet, string $value): Bill
    {
        if (!$sender_wallet->isUserOwner($this->user)) {
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

        return BillRepository::createTransfer($sender_wallet, $recipient_wallet, $value);
    }
}
