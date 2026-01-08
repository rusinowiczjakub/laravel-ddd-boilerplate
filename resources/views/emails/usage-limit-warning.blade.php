@extends('emails.layouts.clean')

@section('content')
    <div class="logo">
        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 2L2 7L12 12L22 7L12 2Z" stroke="#18181b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2 17L12 22L22 17" stroke="#18181b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M2 12L12 17L22 12" stroke="#18181b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </div>

    <h1>⚠️ Usage Limit Warning</h1>

    <p>Your workspace <strong>{{ $workspaceName }}</strong> has reached <strong>{{ $percentageUsed }}%</strong> of your event limit for this month.</p>

    <div style="background-color: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin: 24px 0; border-radius: 4px;">
        <p style="margin: 0; font-weight: 600; color: #92400e;">Current Usage:</p>
        <p style="margin: 8px 0 0 0; color: #92400e; font-size: 18px;">
            <strong>{{ $currentUsage }}</strong> / {{ $limit }} events
        </p>
    </div>

    <p>To ensure uninterrupted service, we recommend upgrading your plan before reaching the limit.</p>

    <a href="{{ $upgradeUrl }}" style="display: inline-block; background-color: #18181b; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 6px; margin: 24px 0; font-weight: 600;">
        Upgrade Your Plan
    </a>

    <p style="font-size: 14px; color: #6b7280;">
        <strong>What happens at 100%?</strong><br>
        @if($plan === 'free')
            Your workspace will stop accepting new events until next month. Upgrade to avoid interruption.
        @else
            Your events will continue to be processed. Any usage beyond your limit may incur additional charges.
        @endif
    </p>

    <div class="signature">
        <p>Thanks,<br>
        <strong>{{ config('app.name') }} Team</strong></p>
    </div>
@endsection
