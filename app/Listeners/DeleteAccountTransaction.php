<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Traits\TransactionUtil;
use App\Events\InvoiceDeleted;
class DeleteAccountTransaction
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
    public function handle($event)
    {
        if($event->invoice->type=='advance'){
            $this->updateContactBalance($event->invoice->client_id, $event->invoice->amount);
        }
        //
    }
}
