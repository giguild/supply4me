<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Receipt {{ $payment->payment_number }}</title>
    @php
        $currency = strtoupper($payment->currency_code ?? 'NGN') === 'NGN' ? '₦' : ($payment->currency_code . ' ');
        $company = $payment->company;
        $incoming = $payment->payment_type === App\Enums\Payments\PaymentType::Incoming;
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
        .status-completed { background: #d1fae5; color: #047857; }
        .status-approved { background: #d1fae5; color: #047857; }
        .status-pending { background: #fef3c7; color: #b45309; }
        .status-rejected { background: #fee2e2; color: #b91c1c; }
        .status-cancelled { background: #f3f4f6; color: #6b7280; }
        .party-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 4px; }
        .party-name { font-weight: bold; font-size: 14px; color: #111827; }
        .party-detail { font-size: 11px; color: #4b5563; }
        .section { margin-top: 16px; }
        h3.section-title { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #9ca3af; margin-bottom: 6px; }
        .details td { padding: 4px 0; font-size: 11px; }
        .details td.label { color: #9ca3af; width: 150px; }
        .details td.value { color: #111827; font-weight: 600; }
        .amount-row td { font-size: 28px; color: #111827; padding: 14px 0; }
        .details .amount-row td.value { font-weight: normal; }
        .sum-table { width: 320px; }
        .allocations th { font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 2px solid #e5e7eb; padding: 6px; text-align: left; }
        .allocations td { font-size: 11px; padding: 6px; border-bottom: 1px solid #f3f4f6; }
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
                <div class="doc-title">{{ $incoming ? 'Payment Receipt' : 'Payment Voucher' }}</div>
                <div class="meta">
                    <span class="badge status-{{ str_replace('_', '-', $payment->status->value) }}">{{ ucwords(str_replace('_', ' ', $payment->status->value)) }}</span><br>
                    <span style="font-weight:bold;">{{ $payment->payment_number }}</span><br>
                    <span class="meta-label">Payment Date:</span> {{ $payment->payment_date?->format('d M Y') ?? '—' }}<br>
                    @if ($payment->branch)
                        <span class="meta-label">Branch:</span> {{ $payment->branch->name }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <table class="details">
        @if ($incoming)
            <tr>
                <td class="label" valign="top">Paid By</td>
                <td class="value">
                    {{ $payment->customer?->name ?? ('Customer #' . $payment->customer_id) }}
                    @if ($payment->customer?->email)<div style="font-weight:normal; color:#6b7280; font-size:11px;">{{ $payment->customer->email }}</div>@endif
                    @if ($payment->customer?->phone)<div style="font-weight:normal; color:#6b7280; font-size:11px;">{{ $payment->customer->phone }}</div>@endif
                </td>
            </tr>
        @else
            <tr>
                <td class="label" valign="top">Paid To</td>
                <td class="value">
                    {{ $payment->supplier?->name ?? ('Supplier #' . $payment->supplier_id) }}
                    @if ($payment->supplier?->email)<div style="font-weight:normal; color:#6b7280; font-size:11px;">{{ $payment->supplier->email }}</div>@endif
                    @if ($payment->supplier?->phone)<div style="font-weight:normal; color:#6b7280; font-size:11px;">{{ $payment->supplier->phone }}</div>@endif
                </td>
            </tr>
        @endif
        <tr class="amount-row">
            <td class="label">Amount</td>
            <td class="value">{{ $currency }}{{ number_format((float) $payment->amount, 2) }}</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td class="value">{{ ucwords(str_replace('_', ' ', $payment->payment_method?->value ?? 'other')) }}</td>
        </tr>
        <tr>
            <td class="label">Reference Number</td>
            <td class="value">{{ $payment->reference_number ?? '—' }}</td>
        </tr>
        @if ($payment->check_number)
            <tr><td class="label">Cheque Number</td><td class="value">{{ $payment->check_number }}</td></tr>
        @endif
        @if ($payment->bank_name)
            <tr><td class="label">Bank</td><td class="value">{{ $payment->bank_name }}@if ($payment->bank_account_number) ({{ $payment->bank_account_number }})@endif</td></tr>
        @endif
        <tr>
            <td class="label">Received By</td>
            <td class="value">{{ $payment->receivedBy?->name ?? '—' }}</td>
        </tr>
        @if ($payment->approvedBy && $payment->approved_at)
            <tr>
                <td class="label">Approved By</td>
                <td class="value">{{ $payment->approvedBy->name }}@if ($payment->approved_at) · {{ $payment->approved_at->format('d M Y H:i') }}@endif</td>
            </tr>
        @endif
    </table>

    @if ($payment->allocations && $payment->allocations->count())
        <div class="section">
            <h3 class="section-title">Invoice Allocation</h3>
            <table class="allocations">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th align="right">Invoice Total</th>
                        <th align="right">Paid</th>
                        <th align="right">Outstanding</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payment->allocations as $allocation)
                        <tr>
                            <td>{{ $allocation->invoice->invoice_number ?? ('#' . $allocation->invoice_id) }}</td>
                            <td align="right">{{ $currency }}{{ number_format((float) $allocation->invoice?->total_amount, 2) }}</td>
                            <td align="right">{{ $currency }}{{ number_format((float) $allocation->invoice?->paid_amount, 2) }}</td>
                            <td align="right">{{ $currency }}{{ number_format((float) $allocation->invoice?->due_amount, 2) }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $allocation->invoice?->status->value ?? '—')) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    @if ($payment->notes)
        <div class="section">
            <h3 class="section-title">Notes</h3>
            <div class="party-detail" style="white-space:pre-line;">{{ $payment->notes }}</div>
        </div>
    @endif

    @if ($payment->rejection_reason)
        <div class="section">
            <h3 class="section-title">Rejection Reason</h3>
            <div class="party-detail" style="color:#b91c1c;">{{ $payment->rejection_reason }}</div>
        </div>
    @endif

    <div class="footer">
        Generated by {{ config('app.name') }} · {{ now()->format('Y-m-d H:i') }}
    </div>
</body>
</html>