@extends('emails.layouts.app')

@section('content')
    @php
        $currency = strtoupper($invoice->currency_code ?? 'NGN') === 'NGN' ? '₦' : ($invoice->currency_code . ' ');
    @endphp
    <h2 class="title">Invoice {{ $invoice->invoice_number }}</h2>
    <p class="message">
        Hi {{ $invoice->customer->name ?? 'there' }}, your invoice is available below.
        The PDF version is attached to this email.
    </p>

    <p class="detail-label">Invoice Date</p>
    <p class="detail-value">{{ $invoice->invoice_date?->format('d M Y') ?? '—' }}</p>
    @if ($invoice->due_date)
        <p class="detail-label">Due Date</p>
        <p class="detail-value">{{ $invoice->due_date->format('d M Y') }}</p>
    @endif
    <p class="detail-label">Balance Due</p>
    <p class="detail-value amount">{{ $currency }}{{ number_format((float) ($invoice->due_amount ?? $invoice->total_amount), 2) }}</p>

    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn">View Invoice</a>
@endsection

@section('footer-text', 'You are receiving this because an invoice was generated for you on SUPPLY4ME.')