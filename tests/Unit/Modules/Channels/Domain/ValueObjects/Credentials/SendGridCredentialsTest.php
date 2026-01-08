<?php

declare(strict_types=1);

use Modules\Channels\Domain\ValueObjects\Credentials\SendGridCredentials;

it('creates SendGrid credentials with all fields', function () {
    $credentials = new SendGridCredentials(
        apiKey: 'SG.xxxxxxxxxxxx',
        fromEmail: 'noreply@example.com',
        fromName: 'My App',
    );

    expect($credentials->apiKey())->toBe('SG.xxxxxxxxxxxx')
        ->and($credentials->fromEmail())->toBe('noreply@example.com')
        ->and($credentials->fromName())->toBe('My App');
});

it('creates SendGrid credentials from array', function () {
    $data = [
        'api_key' => 'SG.test-api-key',
        'from_email' => 'sender@example.com',
        'from_name' => 'Sender',
    ];

    $credentials = SendGridCredentials::fromArray($data);

    expect($credentials->apiKey())->toBe('SG.test-api-key')
        ->and($credentials->fromEmail())->toBe('sender@example.com')
        ->and($credentials->fromName())->toBe('Sender');
});

it('converts SendGrid credentials to array', function () {
    $credentials = new SendGridCredentials(
        apiKey: 'SG.key',
        fromEmail: 'from@example.com',
        fromName: 'From',
    );

    expect($credentials->toArray())->toBe([
        'api_key' => 'SG.key',
        'from_email' => 'from@example.com',
        'from_name' => 'From',
    ]);
});

it('defaults to empty from_name when not provided', function () {
    $credentials = SendGridCredentials::fromArray([
        'api_key' => 'SG.key',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials->fromName())->toBe('');
});
