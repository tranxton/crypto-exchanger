<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\Bill\Bill;
use App\Models\Bill\History;
use Exception;
use Illuminate\Support\Facades\Log;

class BillObserver
{
    public $afterCommit = true;

    /**
     * Handle the Bill "created" event.
     *
     * @param \App\Models\Bill\Bill $bill
     *
     * @return void
     */
    public function created(Bill $bill)
    {
        $bill_history = [
            'bill_id' => $bill->id,
            'action'  => 'bill.created',
            'payload' => $bill->toJson(),
        ];

        try {
            History::create($bill_history);
        } catch (Exception $e) {
            Log::error("Can't write bill history", ['error' => $e->getMessage(), 'bill_history' => $bill_history]);
        }
    }

    /**
     * Handle the Bill "updated" event.
     *
     * @param \App\Models\Bill\Bill $bill
     *
     * @return void
     */
    public function updated(Bill $bill)
    {
        $bill_history = [
            'bill_id' => $bill->id,
            'action'  => 'bill.updated',
            'payload' => $bill->toJson(),
        ];

        try {
            History::create($bill_history);
        } catch (Exception $e) {
            Log::error("Can't write bill history", ['error' => $e->getMessage(), 'bill_history' => $bill_history]);
        }
    }
}
