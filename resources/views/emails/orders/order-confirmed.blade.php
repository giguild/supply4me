@extends('emails.layouts.app')

@section('content')
    @php
        $currency = strtoupper($order->currency_code ?? 'NGN') === 'NGN' ? '₦' : ($order->currency_code . ' ');
    @endphp
    <h2 class="title">Order Confirmed</h2>
    <p class="message">
        Thank you {{ $order->customer->name ?? 'for your order' }}! Your order
        <strong>{{ $order->order_number }}</strong> has been confirmed and is being processed.
    </p>

    @if ($order->items && $order->items->count())
        <table class="items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th class="num">Qty</th>
                    <th class="num">Price</th>
                    <th class="num">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td class="num">{{ rtrim(rtrim(number_format((float) $item->quantity, 2), '0'), '.') }}</td>
                        <td class="num">{{ $currency }}{{ number_format((float) $item->unit_price, 2) }}</td>
                        <td class="num">{{ $currency }}{{ number_format((float) $item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="detail-label">Total</p>
    <p class="detail-value amount">{{ $currency }}{{ number_format((float) $order->total_amount, 2) }}</p>

    @if (isset($invoice) && $invoice)
        <p class="message">An invoice ({{ $invoice->invoice_number }}) has been generated and is attached to this email.</p>
    @endif

    <a href="{{ url('/storefront/login') }}" class="btn">View My Order</a>
@endsection

@section('footer-text', 'You are receiving this because you placed an order on SUPPLY4ME.')