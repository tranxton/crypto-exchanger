<?php

declare(strict_types=1);

namespace App\Services;

use App\Helpers\BCMathHelper;
use App\Jobs\Bill\Transfer as BillTransferJob;
use App\Models\Bill\Bill;
use App\Models\Bill\Status;
use App\Models\User\User;
use App\Models\Wallet\Wallet;
use App\Repositories\BillRepository;
use Exception;

class BillService
{
    use BCMathHelper;

    /**
     * @var User
     */
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Получить счет
     *
     * @param int $id
     *
     * @return Bill
     * @throws Exception
     */
    public function get(int $id): Bill
    {
        $bill = BillRepository::get($id);
        if (!$bill->sender_wallet->isUserOwner($this->user) && !$bill->recipient_wallet->isUserOwner($this->user)) {
            throw new Exception('Нельзя просматривать чужие платежи', 401);
        }

        return $bill;
    }

    /**
     * Создание запроса на перевод
     *
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

        return BillRepository::create($this->user, $sender_wallet, $recipient_wallet, $value);
    }

    /**
     * Перевод статуса счета в "Принят"
     *
     * @param Bill $bill
     *
     * @return Bill
     * @throws Exception
     */
    public function accept(Bill $bill): Bill
    {
        if (!$bill->isUserOwner($this->user)) {
            throw new Exception('Нельзя изменить статус чужого счета', 401);
        }

        if ($bill->status->id !== Status::CREATED) {
            throw new Exception('Нельзя изменить статус счета', 422);
        }

        $bill = BillRepository::accept($bill);

        BillTransferJob::dispatch($bill);

        return $bill;
    }
}
