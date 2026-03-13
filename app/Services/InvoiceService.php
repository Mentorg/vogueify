<?php

namespace App\Services;

use App\Enums\InvoiceStatus;
use App\Events\Order\InvoiceCreated;
use App\Models\Invoice;
use App\Models\InvoiceCounter;
use App\Notifications\Invoice\ResendInvoiceNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Spatie\LaravelPdf\Facades\Pdf;

class InvoiceService
{
    protected function generateSequentialNumber(): string
    {
        $year = now()->year;

        $counter = InvoiceCounter::where('year', $year)
            ->lockForUpdate()
            ->first();

        if (!$counter) {
            $counter = InvoiceCounter::create([
                'year' => $year,
                'current_number' => 0,
            ]);
        }

        $counter->increment('current_number');

        return 'INV-' . $year . '-' .
            str_pad($counter->current_number, 6, '0', STR_PAD_LEFT);
    }

    public function getInvoices()
    {
        return Invoice::with(['items', 'user', 'order'])->orderBy('created_at', 'desc')->paginate(15, ['*'], 'invoices_page');
    }

    public function generatePdf(Invoice $invoice): string
    {
        $invoice->load(['items', 'user', 'order']);

        $basePath = 'invoices/' . $invoice->issued_at->format('Y/m/');
        $archivePath = $basePath . 'archive/';

        Storage::disk('public')->makeDirectory($basePath);
        Storage::disk('public')->makeDirectory($archivePath);

        $baseFileName = $invoice->invoice_number . '.pdf';
        $filePath = $basePath . $baseFileName;

        if (Storage::disk('public')->exists($filePath)) {
            $timestamp = now()->format('YmdHis');

            $archivedFile = $archivePath .
                str_replace('.pdf', "-{$timestamp}.pdf", $baseFileName);

            Storage::disk('public')->move($filePath, $archivedFile);
        }

        Pdf::view('pdf.invoice', ['invoice' => $invoice])
            ->disk('public')
            ->save($filePath);

        $invoice->update([
            'file_path' => $filePath,
            'pdf_generated' => true,
        ]);

        return $filePath;
    }

    public function createFromOrder($order): Invoice
    {
        $order->load([
            'items.productVariation.product',
            'user'
        ]);

        $invoice = DB::transaction(function () use ($order) {

            if ($existing = $order->invoice()->first()) {
                return $existing;
            }

            $invoiceNumber = $this->generateSequentialNumber();

            $invoice = Invoice::create([
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'invoice_number' => $invoiceNumber,
                'currency' => $order->currency ?? 'USD',
                'subtotal' => $order->subtotal,
                'tax_amount' => $order->tax_amount,
                'shipping_cost' => $order->shipping_cost,
                'discount_amount' => $order->discount_amount,
                'total' => $order->total,
                'issued_at' => now(),
                'pdf_generated' => false,
            ]);

            foreach ($order->items as $item) {

                $productName = $item->productVariation?->product?->name
                    ?? $item->productVariation?->sku
                    ?? 'Unknown Product';

                $unitPrice = $item->price_at_time
                    ?? $item->productVariation?->price
                    ?? 0;

                $invoice->items()->create([
                    'product_image' => $item->productVariation?->image,
                    'product_name' => $productName,
                    'product_sku' => $item->productVariation?->sku,
                    'unit_price'   => $unitPrice,
                    'quantity'     => $item->quantity,
                    'total'        => $unitPrice * $item->quantity,
                ]);
            }

            return $invoice;
        });

        DB::afterCommit(fn () => event(new InvoiceCreated($invoice)));

        return $invoice;
    }

    public function getInvoice($invoice)
    {
        return $invoice->load('items', 'user', 'order');
    }

    public function download($invoice)
    {
        $invoice->refresh();

        if (
            ! $invoice->file_path ||
            ! Storage::disk('public')->exists($invoice->file_path)
        ) {
            $this->generatePdf($invoice);
            $invoice->refresh();
        }

        if (! Storage::disk('public')->exists($invoice->file_path)) {
            abort(404, 'Invoice PDF not found!');
        }

        if ($invoice->status === InvoiceStatus::CANCELLED) {
            $this->generatePdf($invoice);
        }

        $disk = Storage::disk('public');

        return $disk->download(
            $invoice->file_path,
            $invoice->invoice_number . '.pdf'
        );
    }

    public function resend($invoice)
    {
        $invoice->load(['items', 'user', 'order']);

        $user = $invoice->user;

        $user->notify(new ResendInvoiceNotification($invoice));
    }

    public function cancel($invoice)
    {
        $invoice->status = InvoiceStatus::CANCELLED;

        return $invoice->save();
    }

    public function updateNote($validated, $invoice)
    {
        $invoice->update([
            'internal_note' => $validated['internal_note']
        ]);
    }
}
