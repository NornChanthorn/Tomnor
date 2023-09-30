<?php

namespace App\Listeners;

use App\Traits\TransactionUtil;
use App\Events\InvoiceAdded;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddAccountTransaction
{
    use TransactionUtil;
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
     * @param  object  $event
     * @return void
     */
    public function handle(InvoiceAdded $event)
    {
        //Add contact advance if exists
        if ($event->invoice->type == 'advance') {
            $this->updateContactBalance($event->invoice->client_id, $event->invoice->amount, 'deduct');
        }
        //
    }
}
