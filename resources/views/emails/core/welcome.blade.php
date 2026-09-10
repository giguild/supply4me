@extends('emails.layouts.app')

@section('content')
    <h2 class="title">Welcome, {{ $user->name }}!</h2>
    <p class="message">
        Your account has been created on SUPPLY4ME. Use the credentials below to sign in to the ERP.
    </p>

    <p class="detail-label">Email Address</p>
    <p class="detail-value">{{ $user->email }}</p>

    @if ($password)
        <p class="detail-label">Password</p>
        <p class="detail-value">{{ $password }}</p>
    @endif

    @if (count($roleNames))
        <p class="detail-label">Role</p>
        <p class="detail-value">{{ implode(', ', $roleNames) }}</p>
    @endif

    <p class="detail-label">ERP Login URL</p>
    <p class="detail-value">{{ 'https://supply4me.ng/erp/login' }}</p>

    <a href="{{ 'https://supply4me.ng/erp/login' }}" class="btn">Log In to ERP</a>
@endsection

@section('footer-text', 'You are receiving this because an account was created for you on SUPPLY4ME.')