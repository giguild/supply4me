@extends('emails.layouts.app')

@section('content')
    @php
        $currency = strtoupper($payment->currency_code ?? 'NGN') === 'NGN' ? '₦' : ($payment->currency_code . ' ');
    @endphp
    <h2 class="title">Payment Confirmed</h2>
    <p class="message">
        Hi {{ $payment->customer->name ?? 'there' }}, we have received your payment. A receipt PDF is attached.
    </p>

    <p class="detail-label">Payment Number</p>
    <p class="detail-value">{{ $payment->payment_number }}</p>
    <p class="detail-label">Payment Date</p>
    <p class="detail-value">{{ $payment->payment_date?->format('d M Y') ?? '—' }}</p>
    <p class="detail-label">Method</p>
    <p class="detail-value">{{ ucwords(str_replace('_', ' ', $payment->payment_method?->value ?? 'other')) }}</p>
    @if ($payment->reference_number)
        <p class="detail-label">Reference</p>
        <p class="detail-value">{{ $payment->reference_number }}</p>
    @endif
    <p class="detail-label">Amount</p>
    <p class="detail-value amount">{{ $currency }}{{ number_format((float) $payment->amount, 2) }}</p>

    <a href="{{ route('payments.show', $payment->id) }}" class="btn">View Payment</a>
@endsection

@section('footer-text', 'You are receiving this because a payment was recorded for you on SUPPLY4ME.')