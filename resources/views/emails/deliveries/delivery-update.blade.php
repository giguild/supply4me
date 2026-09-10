@extends('emails.layouts.app')

@section('content')
    <h2 class="title">Delivery Update</h2>
    <p class="message">
        Hi {{ $delivery->customer->name ?? 'there' }}, the delivery for order
        <strong>{{ $delivery->order?->order_number ?? '#' . $delivery->order_id }}</strong>
        ({{ $delivery->delivery_number }}) {{ $statusText }}.
    </p>

    @if ($delivery->status->value === 'delivered' && $delivery->actual_delivery_date)
        <p class="detail-label">Delivered On</p>
        <p class="detail-value">{{ $delivery->actual_delivery_date->format('d M Y') }}</p>
    @endif
    @if (!empty($failureReason))
        <p class="detail-label">Reason</p>
        <p class="detail-value">{{ $failureReason }}</p>
    @endif

    <a href="{{ route('deliveries.show', $delivery->id) }}" class="btn">View Delivery</a>
@endsection

@section('footer-text', 'You are receiving this because a delivery was scheduled to you on SUPPLY4ME.')