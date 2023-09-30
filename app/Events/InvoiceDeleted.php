<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;


class InvoiceDeleted
{
    use SerializesModels;

    public $transactionPaymentId;
    public $accountId;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
        //
    }
}
