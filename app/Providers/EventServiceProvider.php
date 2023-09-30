<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
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
        // Registered::class => [
        //     SendEmailVerificationNotification::class,
        // ],
        \App\Events\InvoiceAdded::class => [
            \App\Listeners\AddAccountTransaction::class,
        ],

        \App\Events\InvoiceUpdated::class => [
            \App\Listeners\UpdateAccountTransaction::class,
        ],

        \App\Events\InvoiceDeleted::class => [
            \App\Listeners\DeleteAccountTransaction::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
