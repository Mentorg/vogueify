<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Services\InvoiceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    use AuthorizesRequests;

    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    public function show(Invoice $invoice)
    {
        $this->authorize('view', $invoice);

        return Inertia::render('Admin/InvoiceDetails', [
            'invoice' => $this->invoiceService->getInvoice($invoice)
        ]);
    }

    public function download(Invoice $invoice)
    {
        $this->authorize('download', $invoice);

        return $this->invoiceService->download($invoice);
    }

    public function resend(Invoice $invoice)
    {
        $this->authorize('resend', $invoice);

        return $this->invoiceService->resend($invoice);
    }

    public function cancel(Invoice $invoice)
    {
        $this->authorize('cancel', $invoice);

        $this->invoiceService->cancel($invoice);

        return redirect()->back()->with('success', 'Invoice cancelled successfully.');
    }

    public function updateNote(Request $request, Invoice $invoice)
    {
        $this->authorize('updateNote', $invoice);

        $validated = $request->validate([
            'internal_note' => 'nullable|string|max:65535'
        ]);

        $this->invoiceService->updateNote($validated, $invoice);
    }
}
