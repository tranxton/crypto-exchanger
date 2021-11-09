<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TransferTransactionCompletedEvent;

class ChargeBonusToUsersListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  TransferTransactionCompletedEvent $event
     *
     * @return void
     */
    public function handle(TransferTransactionCompletedEvent $event)
    {
        $bill = $event->bill;
    }
}
