<?php

declare(strict_types=1);

use Modules\Channels\Domain\ValueObjects\Credentials\PostmarkCredentials;

it('creates Postmark credentials with all fields', function () {
    $credentials = new PostmarkCredentials(
        serverToken: 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx',
        fromEmail: 'noreply@example.com',
        fromName: 'My App',
    );

    expect($credentials->serverToken())->toBe('xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx')
        ->and($credentials->fromEmail())->toBe('noreply@example.com')
        ->and($credentials->fromName())->toBe('My App');
});

it('creates Postmark credentials from array', function () {
    $data = [
        'server_token' => 'test-token-123',
        'from_email' => 'sender@example.com',
        'from_name' => 'Sender',
    ];

    $credentials = PostmarkCredentials::fromArray($data);

    expect($credentials->serverToken())->toBe('test-token-123')
        ->and($credentials->fromEmail())->toBe('sender@example.com')
        ->and($credentials->fromName())->toBe('Sender');
});

it('converts Postmark credentials to array', function () {
    $credentials = new PostmarkCredentials(
        serverToken: 'token-abc',
        fromEmail: 'from@example.com',
        fromName: 'From',
    );

    expect($credentials->toArray())->toBe([
        'server_token' => 'token-abc',
        'from_email' => 'from@example.com',
        'from_name' => 'From',
    ]);
});

it('defaults to empty from_name when not provided', function () {
    $credentials = PostmarkCredentials::fromArray([
        'server_token' => 'token',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials->fromName())->toBe('');
});
