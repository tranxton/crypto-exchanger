<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Bill\Bill;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferTransactionCompletedEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $bill;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Bill $bill)
    {
        $this->bill = $bill;
    }
}
