<?php

declare(strict_types=1);

namespace App\Jobs\Bill;

use App\Events\TransferTransactionCompletedEvent;
use App\Models\Bill\Bill;
use App\Models\Transaction\Transaction;
use App\Models\Transaction\Type;
use App\Models\Wallet\SystemWallet;
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
            TransferTransactionCompletedEvent::dispatch($this->bill);
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
}
