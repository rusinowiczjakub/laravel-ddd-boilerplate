<?php

declare(strict_types=1);

return [
    'logout_success' => 'Logged out successfully',
    'verification_code_sent' => 'Verification code sent successfully',
    'authentication_successful' => 'Authentication successful',
    'invalid_phone_number' => 'Invalid phone number format. Expected E.164 format (e.g., +48123456789).',
    'invalid_verification_code' => 'Invalid verification code format. Expected 6-digit numeric code.',
    'too_many_attempts' => 'Too many verification attempts. Please request a new verification code.',
    'session_expired' => 'Verification session has expired. Please request a new verification code.',
    'session_not_found' => 'Verification session not found. The token may be invalid or expired.',
    'service_unavailable' => 'Verification service is currently unavailable. Please try again later.',
    'sending_failed' => 'Failed to send verification code. Please try again.',
    'verification_failed' => 'The verification code is invalid or has expired.',
    'onboarding_already_completed' => 'Onboarding has already been completed.',
];
