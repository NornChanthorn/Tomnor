<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

class InvoiceUpdated
{
    use SerializesModels;

    public $invoice;

    public $transactionType;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Invoice $invoice, $transactionType)
    {
        $this->invoice = $invoice;
        $this->transactionType = $transactionType;
    }
}
