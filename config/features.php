<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Waitlist Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, normal registration and login are disabled.
    | Users can only sign up for the waitlist.
    |
    */
    'waitlist_mode' => env('FEATURE_WAITLIST_MODE', false),

    /*
    |--------------------------------------------------------------------------
    | Channel Availability
    |--------------------------------------------------------------------------
    |
    | Control which notification channels are available.
    | When disabled, users cannot create templates, providers, or workflow
    | steps for that channel.
    |
    */
    'channels' => [
        'email' => true, // Always available
        'sms' => env('FEATURE_SMS_ENABLED', false),
        'push' => env('FEATURE_PUSH_ENABLED', false),
    ],
];
