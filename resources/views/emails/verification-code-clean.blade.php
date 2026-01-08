@extends('emails.layouts.clean')

@section('content')
    <div class="logo">
        {{-- Tutaj możesz dodać swoje logo --}}
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#18181b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2 17L12 22L22 17" stroke="#18181b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2 12L12 17L22 12" stroke="#18181b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <h1>Your verification code</h1>

    <p>To complete your sign-up for {{ config('app.name') }}, please enter the following verification code in the signup form:</p>

    <div class="code-box">
        <div class="code">{{ $code }}</div>
    </div>

    <p>This code will expire in {{ $expiresIn }} minutes.</p>

    <p>If you didn't initiate this request, you can safely ignore this email, no further action is needed.</p>

    <div class="signature">
        <p>Thanks,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
    </div>
@endsection
