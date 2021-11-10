<?php

namespace App\Providers;

use App\Events\TransferTransactionCompleted;
use App\Listeners\ChargeBonusToUsers;
use App\Models\Bill\Bill;
use App\Observers\BillObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        TransferTransactionCompleted::class => [
            ChargeBonusToUsers::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Bill::observe(BillObserver::class);
    }
}
