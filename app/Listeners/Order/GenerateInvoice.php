<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderPaid;
use App\Jobs\Order\GenerateInvoiceJob;
use App\Services\InvoiceService;

class GenerateInvoice
{
    protected $invoiceService;
    /**
     * Create the event listener.
     */
    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaid $event): void
    {
        $order = $event->order->fresh();

        if ($order->invoice()->exists()) {
            return;
        }

        $invoice = app(InvoiceService::class)->createFromOrder($order);

        GenerateInvoiceJob::dispatch($invoice);
    }
}
