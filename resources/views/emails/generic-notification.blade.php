@extends('emails.layouts.app')

@section('content')
    <h2 class="title">{{ $data['title'] }}</h2>
    <p class="message">{{ $data['message'] }}</p>
    @if(!empty($data['action_url']))
        <a href="{{ $data['action_url'] }}" class="btn">{{ $data['action_label'] ?? 'View' }}</a>
    @endif
@endsection