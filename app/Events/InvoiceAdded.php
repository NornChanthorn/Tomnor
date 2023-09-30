<?php

namespace App\Events;

use App\Models\Invoice;
use Illuminate\Queue\SerializesModels;

class InvoiceAdded
{
    use SerializesModels;
    public $invoice;
    public $formInput;

    /**
     * Create a new event instance.
     *
     * @param  Order  $order
     * @param  array $formInput = []
     * @return void
     */
    public function __construct(Invoice $invoice, $formInput = [])
    {
        $this->invoice = $invoice;
        $this->formInput = $formInput;
    }
}
