<?php

declare(strict_types=1);

use Modules\Channels\Domain\ValueObjects\Credentials\MailgunCredentials;

it('creates Mailgun credentials with all fields', function () {
    $credentials = new MailgunCredentials(
        apiKey: 'key-xxxxxxxxxxxx',
        domain: 'mg.example.com',
        fromEmail: 'noreply@mg.example.com',
        fromName: 'My App',
        endpoint: 'api.eu.mailgun.net',
    );

    expect($credentials->apiKey())->toBe('key-xxxxxxxxxxxx')
        ->and($credentials->domain())->toBe('mg.example.com')
        ->and($credentials->fromEmail())->toBe('noreply@mg.example.com')
        ->and($credentials->fromName())->toBe('My App')
        ->and($credentials->endpoint())->toBe('api.eu.mailgun.net');
});

it('creates Mailgun credentials from array', function () {
    $data = [
        'api_key' => 'key-test',
        'domain' => 'mail.example.com',
        'from_email' => 'sender@mail.example.com',
        'from_name' => 'Sender',
        'endpoint' => 'api.mailgun.net',
    ];

    $credentials = MailgunCredentials::fromArray($data);

    expect($credentials->apiKey())->toBe('key-test')
        ->and($credentials->domain())->toBe('mail.example.com')
        ->and($credentials->endpoint())->toBe('api.mailgun.net');
});

it('converts Mailgun credentials to array', function () {
    $credentials = new MailgunCredentials(
        apiKey: 'key-123',
        domain: 'mg.test.com',
        fromEmail: 'from@mg.test.com',
        fromName: 'Test',
        endpoint: 'api.mailgun.net',
    );

    expect($credentials->toArray())->toBe([
        'api_key' => 'key-123',
        'domain' => 'mg.test.com',
        'endpoint' => 'api.mailgun.net',
        'from_email' => 'from@mg.test.com',
        'from_name' => 'Test',
    ]);
});

it('defaults to api.mailgun.net endpoint when not provided', function () {
    $credentials = MailgunCredentials::fromArray([
        'api_key' => 'key-test',
        'domain' => 'mg.example.com',
        'from_email' => 'test@mg.example.com',
    ]);

    expect($credentials->endpoint())->toBe('api.mailgun.net');
});

it('uses EU endpoint when specified', function () {
    $credentials = MailgunCredentials::fromArray([
        'api_key' => 'key-test',
        'domain' => 'mg.example.com',
        'from_email' => 'test@mg.example.com',
        'endpoint' => 'api.eu.mailgun.net',
    ]);

    expect($credentials->endpoint())->toBe('api.eu.mailgun.net');
});
