<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    @php
        $currency = strtoupper($invoice->currency_code ?? 'NGN') === 'NGN' ? '₦' : ($invoice->currency_code . ' ');
        $company = $invoice->company;
    @endphp
    <style>
        * { font-family: DejaVu Sans, sans-serif; }
        body { color: #1f2937; font-size: 12px; line-height: 1.5; }
        table { border-collapse: collapse; width: 100%; }
        .header { border-bottom: 3px solid #111827; padding-bottom: 12px; margin-bottom: 20px; }
        .brand-name { font-size: 20px; font-weight: bold; color: #111827; }
        .brand-detail { color: #6b7280; font-size: 10px; }
        .doc-title { font-size: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 2px; }
        .meta { font-size: 11px; color: #374151; margin-top: 4px; }
        .meta-label { color: #9ca3af; }
        .badge { font-size: 9px; font-weight: bold; text-transform: uppercase; padding: 2px 8px; border-radius: 6px; }
        .status-draft { background: #dbeafe; color: #1d4ed8; }
        .status-sent { background: #fef3c7; color: #b45309; }
        .status-paid { background: #d1fae5; color: #047857; }
        .status-partially-paid { background: #ede9fe; color: #6d28d9; }
        .status-overdue { background: #fee2e2; color: #b91c1c; }
        .status-void { background: #fee2e2; color: #b91c1c; }
        .party-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 4px; }
        .party-name { font-weight: bold; font-size: 14px; color: #111827; }
        .party-detail { font-size: 11px; color: #4b5563; }
        .section { margin-top: 16px; }
        .items th { background: #111827; color: #ffffff; font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; padding: 8px; text-align: left; }
        .items th.num, .items td.num { text-align: right; }
        .items td { font-size: 11px; padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .totals { width: 320px; }
        .totals .row td { font-size: 11px; padding: 3px 0; color: #4b5563; }
        .totals .row td:last-child { text-align: right; }
        .totals .row.total td { border-top: 2px solid #111827; font-weight: bold; font-size: 14px; color: #111827; padding-top: 6px; margin-top: 4px; }
        .totals .row.paid td { color: #047857; font-weight: bold; }
        .totals .row.due td { color: #b91c1c; font-weight: bold; }
        .payments th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 2px solid #e5e7eb; padding: 6px; text-align: left; }
        .payments td { font-size: 11px; padding: 6px; border-bottom: 1px solid #f3f4f6; }
        .footer { border-top: 1px solid #e5e7eb; padding-top: 10px; margin-top: 20px; text-align: center; font-size: 9px; color: #9ca3af; }
    </style>
</head>
<body>
    <table class="header">
        <tr>
            <td width="55%" valign="top">
                @if (file_exists(storage_path('app/branding/logo_dark.png')))
                    <img src="{{ storage_path('app/branding/logo_dark.png') }}" style="height:40px; margin-bottom:6px;" alt="{{ config('app.name') }}" />
                @endif
                <div class="brand-name">{{ $company->name ?? config('app.name') }}</div>
                @if ($company && ($company->address_line_1 || $company->city || $company->state || $company->email || $company->phone || $company->tax_number))
                    <div class="brand-detail">
                        @if ($company->address_line_1){{ $company->address_line_1 }}@endif
                        @if ($company->address_line_2){{ $company->address_line_2 }}@endif
                        @if ($company->city && $company->state){{ $company->city }}, {{ $company->state }}@elseif ($company->city){{ $company->city }}@endif
                    </div>
                    <div class="brand-detail">
                        @if ($company->email){{ $company->email }}@endif
                        @if ($company->email && $company->phone) · @endif
                        @if ($company->phone){{ $company->phone }}@endif
                        @if ($company->tax_number) · Tax No: {{ $company->tax_number }}@endif
                    </div>
                @endif
            </td>
            <td width="45%" valign="top" align="right">
                <div class="doc-title">Invoice</div>
                <div class="meta">
                    <span class="badge status-{{ str_replace('_', '-', $invoice->status->value) }}">{{ ucwords(str_replace('_', ' ', $invoice->status->value)) }}</span><br>
                    <span style="font-weight:bold;">{{ $invoice->invoice_number }}</span><br>
                    <span class="meta-label">Invoice Date:</span> {{ $invoice->invoice_date->format('d M Y') }}<br>
                    @if ($invoice->due_date)
                        <span class="meta-label">Due Date:</span> {{ $invoice->due_date->format('d M Y') }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table>
        <tr>
            <td width="50%" valign="top">
                <div class="party-title">Bill To</div>
                <div class="party-name">{{ $invoice->customer?->name }}</div>
                @if ($invoice->customer?->trade_name)<div class="party-detail">{{ $invoice->customer->trade_name }}</div>@endif
                @if ($invoice->customer?->email)<div class="party-detail">{{ $invoice->customer->email }}</div>@endif
                @if ($invoice->customer?->phone)<div class="party-detail">{{ $invoice->customer->phone }}</div>@endif
                @if ($invoice->customer?->address_line_1)<div class="party-detail">{{ $invoice->customer->address_line_1 }}@if ($invoice->customer->address_line_2), {{ $invoice->customer->address_line_2 }}@endif</div>@endif
                @if ($invoice->customer?->city || $invoice->customer?->state)<div class="party-detail">{{ $invoice->customer->city }}@if ($invoice->customer->state), {{ $invoice->customer->state }}@endif</div>@endif
            </td>
            <td width="50%" valign="top">
                @if ($invoice->notes || $invoice->terms_conditions)
                    <div class="party-title">Notes</div>
                    @if ($invoice->notes)<div class="party-detail">{{ $invoice->notes }}</div>@endif
                    @if ($invoice->terms_conditions)<div class="party-detail">{{ $invoice->terms_conditions }}</div>@endif
                @endif
            </td>
        </tr>
    </table>

    <div class="section">
        <table class="items">
            <thead>
                <tr>
                    <th>Description</th>
                    <th class="num">Qty</th>
                    <th class="num">Unit Price</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoice->items as $item)
                    <tr>
                        <td>
                            <span style="font-weight:bold;">{{ $item->name }}</span>
                            @if ($item->sku)<br><span style="color:#9ca3af; font-size:10px;">SKU: {{ $item->sku }}</span>@endif
                        </td>
                        <td class="num">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                        <td class="num">{{ $currency }}{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="num">{{ $currency }}{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" align="center" style="color:#9ca3af;">No items on this invoice.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="section">
                    <table class="totals" style="margin-left:auto;">
                        <tr class="row"><td>Subtotal</td><td>{{ $currency }}{{ number_format((float) $invoice->subtotal, 2) }}</td></tr>
                        @if ((float) $invoice->discount_amount > 0)
                            <tr class="row"><td>Discount</td><td>-{{ $currency }}{{ number_format((float) $invoice->discount_amount, 2) }}</td></tr>
                        @endif
                        <tr class="row"><td>Tax</td><td>{{ $currency }}{{ number_format((float) $invoice->tax_amount, 2) }}</td></tr>
                        @if ((float) $invoice->shipping_amount > 0)
                            <tr class="row"><td>Shipping</td><td>{{ $currency }}{{ number_format((float) $invoice->shipping_amount, 2) }}</td></tr>
                        @endif
                        <tr class="row total"><td>Total</td><td>{{ $currency }}{{ number_format((float) $invoice->total_amount, 2) }}</td></tr>
                        @if ((float) $invoice->paid_amount > 0)
                            <tr class="row paid"><td>Paid</td><td>-{{ $currency }}{{ number_format((float) $invoice->paid_amount, 2) }}</td></tr>
                            <tr class="row due"><td>Balance Due</td><td>{{ $currency }}{{ number_format((float) $invoice->due_amount, 2) }}</td></tr>
                        @endif
                    </table>
    </div>

    @if ($invoice->payments && $invoice->payments->count())
        <div class="section">
            <table class="payments">
                <thead>
                    <tr>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th align="right">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->payment_number }}</td>
                            <td>{{ $payment->payment_date?->format('d M Y') ?? '—' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method?->value ?? 'other')) }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $payment->status->value ?? 'pending')) }}</td>
                            <td align="right">{{ $currency }}{{ number_format((float) $payment->amount, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="footer">
        Generated by {{ config('app.name') }} · {{ $invoice->createdBy?->name ?? 'System' }} · {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>