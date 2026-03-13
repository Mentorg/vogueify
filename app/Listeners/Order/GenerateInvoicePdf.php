<?php

namespace App\Listeners\Order;

use App\Events\Order\InvoiceCreated;
use App\Jobs\Order\GenerateInvoicePdfJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateInvoicePdf implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(InvoiceCreated $event): void
    {
        if (!$event->invoice->pdf_generated) {
            GenerateInvoicePdfJob::dispatch($event->invoice);
        }
    }
}
