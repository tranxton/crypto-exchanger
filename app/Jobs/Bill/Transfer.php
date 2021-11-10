<?php

declare(strict_types=1);

namespace App\Jobs\Bill;

use App\Events\TransferTransactionCompleted;
use App\Models\Bill\Bill;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\Type;
use App\Models\User\User;
use App\Models\Wallet\SystemWallet;
use App\Models\Wallet\Wallet;
use App\Repositories\BillRepository;
use App\Repositories\UserRepository;
use App\Repositories\WalletRepository;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Transfer implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private $bill;

    private $sender_wallet;

    private $recipient_wallet;

    private $system_wallet;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
        $this->sender_wallet = $this->bill->sender_wallet;
        $this->recipient_wallet = $this->bill->recipient_wallet;
        $this->system_wallet = SystemWallet::getByCurrency($this->sender_wallet->currency->id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->bill->isExpired()) {
            $this->bill->expire();

            return;
        }

        DB::beginTransaction();
        try {
            /**
             * @var Transaction $transaction
             */
            foreach ($this->bill->transactions as $transaction) {
                $this->completeTransaction($transaction);
            }

            if (!$this->bill->complete()) {
                throw new Exception("Could not save bill changes");
            }

            $this->dropCaches($this->bill, $this->sender_wallet, $this->recipient_wallet);

            TransferTransactionCompleted::dispatch($this->bill);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Bill transfer not completed", ['error' => $e->getMessage(), 'bill' => $this->bill->toJson()]);
            $this->bill->fail();

            return;
        }

        DB::commit();
    }

    /**
     * Завершает процессинг транзакции
     *
     * @param Transaction $transaction
     *
     * @return void
     * @throws Exception
     */
    private function completeTransaction(Transaction $transaction)
    {
        switch ($transaction->type->id) {
            case Type::TRANSFER:
                if (!$this->completeTransferTransaction($transaction)) {
                    throw new Exception("Could not complete transfer transaction ID {$transaction->id}");
                }
                break;
            case Type::COMMISSION:
                if (!$this->completeCommissionTransaction($transaction)) {
                    throw new Exception("Could not complete commission transaction ID {$transaction->id}");
                }
                break;
            default:
                throw new Exception("Unknown type of transaction passed");
        }
    }

    /**
     * Завершает процессинг перевода
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    private function completeTransferTransaction(Transaction $transaction): bool
    {
        $this->sender_wallet->decreaseBalance($transaction->value);
        $this->recipient_wallet->increaseBalance($transaction->value);

        return $this->sender_wallet->save() && $this->recipient_wallet->save();
    }

    /**
     * Завершает процессинг комиссии
     *
     * @param Transaction $transaction
     *
     * @return bool
     */
    private function completeCommissionTransaction(Transaction $transaction): bool
    {
        $this->sender_wallet->decreaseBalance($transaction->value);
        $this->system_wallet->increaseBalance($transaction->value);

        return $this->sender_wallet->save() && $this->system_wallet->save();
    }

    /**
     * Сбрасывает кэши
     *
     * @param Bill   $bill
     * @param Wallet $sender_wallet
     * @param Wallet $recipient_wallet
     */
    private function dropCaches(Bill $bill, Wallet $sender_wallet, Wallet $recipient_wallet)
    {
        $this->dropBillCache($bill);

        $this->dropWalletCache($sender_wallet);
        $this->dropWalletCache($recipient_wallet);

        $this->dropUserCache($sender_wallet->user);
        $this->dropUserCache($recipient_wallet->user);
    }

    /**
     * Сбрасывает кэш счета
     *
     * @param Bill $bill
     */
    private function dropBillCache(Bill $bill): void
    {
        BillRepository::cacheBill($bill);
        BillRepository::dropCacheUserBills($bill->user);
    }

    /**
     * Сбрасывает кэш пользователя
     *
     * @param User $user
     */
    private function dropUserCache(User $user): void
    {
        UserRepository::cacheUser($user);
    }

    /**
     * Сбрасывает кэш кошелька
     *
     * @param Wallet $wallet
     */
    private function dropWalletCache(Wallet $wallet): void
    {
        WalletRepository::cacheWallet($wallet);
    }
}
