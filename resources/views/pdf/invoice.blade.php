<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        @page {
            margin: 60px 60px 80px 60px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
        }

        .container {
            width: 100%;
        }

        h1 {
            font-size: 22px;
            margin: 0 0 10px 0;
        }

        .header-table {
            width: 100%;
            margin-bottom: 30px;
        }

        .header-table td {
            vertical-align: top;
        }

        .invoice-meta {
            text-align: right;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .items-table th {
            background-color: #f5f5f5;
            text-align: left;
            padding: 8px;
            border-bottom: 1px solid #ddd;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }

        .totals-table {
            width: 40%;
            margin-top: 20px;
            margin-left: auto;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 6px 8px;
        }

        .totals-table tr:last-child td {
            font-weight: bold;
            border-top: 1px solid #ccc;
        }

        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 60px;
            text-align: center;
            font-size: 11px;
            color: #777;
        }

        .watermark {
            position: fixed;
            top: 40%;
            left: 15%;
            width: 70%;
            text-align: center;
            font-size: 90px;
            color: #000;
            opacity: 0.08;
            transform: rotate(-30deg);
            z-index: -1;
        }
    </style>
</head>
<body>

<div class="container">
    @if($invoice->pdf_generated)
        <div class="watermark">PAID</div>
    @endif
    <div class="content">
        <table class="header-table">
            <tr>
                <td>
                    <h1>INVOICE</h1>
                    <div>
                        <strong>VOGUEIFY</strong><br>
                        Kastanienallee 47<br>
                        Berlin, Germany<br>
                        support@vogueify.com
                    </div>
                </td>
                <td class="invoice-meta">
                    <div class="section-title">Invoice Details</div>
                    Invoice #: {{ $invoice->invoice_number }}<br>
                    Date: {{ $invoice->issued_at->format('Y-m-d') }}<br>
                    Order #: {{ $invoice->order->order_number ?? $invoice->order->id }}
                </td>
            </tr>
        </table>

        <div class="bill-to">
            <div class="section-title">Bill To</div>
            <div>{{ $invoice->user->name }}<br>{{ $invoice->user->email }}</div>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 50%;">Product</th>
                    <th style="width: 15%;">Unit Price</th>
                    <th style="width: 15%;">Qty</th>
                    <th style="width: 20%; text-align: right;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $invoice->currency === 'EUR' ? "€" : "$" }}{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td style="text-align: right;">
                            {{ $invoice->currency === 'EUR' ? "€" : "$" }}{{ number_format($item->total, 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals-table">
            <tr>
                <td>Subtotal:</td>
                <td style="text-align: right;">
                    {{ $invoice->currency === 'EUR' ? "€" : "$" }}{{ number_format($invoice->subtotal, 2) }}
                </td>
            </tr>

            @if($invoice->discount_amount > 0)
            <tr>
                <td>Discount:</td>
                <td style="text-align: right;">
                    -{{ $invoice->currency === 'EUR' ? "€" : "$" }}{{ number_format($invoice->discount_amount, 2) }}
                </td>
            </tr>
            @endif

            @if($invoice->shipping_cost > 0)
            <tr>
                <td>Shipping:</td>
                <td style="text-align: right;">
                    {{ $invoice->currency === 'EUR' ? "€" : "$" }}{{ number_format($invoice->shipping_cost, 2) }}
                </td>
            </tr>
            @endif

            @if($invoice->tax_amount > 0)
            <tr>
                <td>Tax:</td>
                <td style="text-align: right;">
                    {{ $invoice->currency === 'EUR' ? "€" : "$" }}{{ number_format($invoice->tax_amount, 2) }}
                </td>
            </tr>
            @endif

            <tr>
                <td>Total:</td>
                <td style="text-align: right; font-weight: bold; font-size: 14px;">
                    {{ $invoice->currency === 'EUR' ? "€" : "$" }}{{ number_format($invoice->total, 2) }}
                </td>
            </tr>
        </table>

        <div style="clear: both;"></div>

        <div class="footer">
            Thank you for your business.<br>
            This invoice was generated electronically.
        </div>
    </div>
</body>
</html>
