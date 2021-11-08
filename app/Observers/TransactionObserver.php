<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Transaction\History;
use App\Models\Transaction\Transaction;
use Exception;
use Illuminate\Support\Facades\Log;

class TransactionObserver
{
    public $afterCommit = true;

    /**
     * Handle the Transaction "created" event.
     *
     * @param \App\Models\Transaction\Transaction $transaction
     *
     * @return void
     */
    public function created(Transaction $transaction)
    {
        $transaction_history = [
            'transaction_id' => $transaction->id,
            'action'  => 'transaction.created',
            'payload' => $transaction->toJson(),
        ];

        try {
            History::create($transaction_history);
        } catch (Exception $e) {
            Log::error("Can't write transaction history", ['error' => $e->getMessage(), 'transaction_history' => $transaction_history]);
        }
    }

    /**
     * Handle the Transaction "updated" event.
     *
     * @param \App\Models\Transaction\Transaction $transaction
     *
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        $transaction_history = [
            'transaction_id' => $transaction->id,
            'action'  => 'transaction.updated',
            'payload' => $transaction->toJson(),
        ];

        try {
            History::create($transaction_history);
        } catch (Exception $e) {
            Log::error("Can't write transaction history", ['error' => $e->getMessage(), 'transaction_history' => $transaction_history]);
        }
    }
}
