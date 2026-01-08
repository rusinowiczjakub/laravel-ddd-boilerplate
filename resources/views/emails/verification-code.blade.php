@component('mail::message')
# Your verification code

To complete your sign-up for {{ config('app.name') }}, please enter the following verification code in the signup form:

<div class="code-block">
    <code>{{ $code }}</code>
</div>

This code will expire in {{ $expiresIn }} minutes.

If you didn't initiate this request, you can safely ignore this email, no further action is needed.

Thanks,<br>
{{ config('app.name') }} Team

@component('mail::subcopy')
© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
@endcomponent
@endcomponent
