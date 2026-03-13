<?php

namespace App\Jobs\Order;

use App\Models\Invoice;
use App\Notifications\Order\OrderConfirmedNotification;
use App\Services\InvoiceService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class GenerateInvoiceJob implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Invoice $invoice)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(InvoiceService $invoiceService)
    {
        $invoice = Invoice::find($this->invoice->id);
        if (!$invoice || $invoice->pdf_generated) {
            return;
        }

        $invoiceService->generatePdf($invoice);

        $invoice->order->user->notify(
            new OrderConfirmedNotification($invoice->order->fresh())
        );
    }
}
