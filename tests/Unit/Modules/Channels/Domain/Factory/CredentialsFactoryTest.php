<?php

declare(strict_types=1);

use Modules\Channels\Domain\Enums\ProviderType;
use Modules\Channels\Domain\Exception\UnsupportedProviderCredentialsException;
use Modules\Channels\Domain\Factory\CredentialsFactory;
use Modules\Channels\Domain\ValueObjects\Credentials\MailgunCredentials;
use Modules\Channels\Domain\ValueObjects\Credentials\PostmarkCredentials;
use Modules\Channels\Domain\ValueObjects\Credentials\SendGridCredentials;
use Modules\Channels\Domain\ValueObjects\Credentials\SESCredentials;
use Modules\Channels\Domain\ValueObjects\Credentials\SMTPCredentials;

it('creates SMTP credentials', function () {
    $factory = new CredentialsFactory();

    $credentials = $factory->create(ProviderType::SMTP, [
        'host' => 'smtp.example.com',
        'port' => '587',
        'username' => 'user',
        'password' => 'pass',
        'encryption' => 'tls',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials)->toBeInstanceOf(SMTPCredentials::class)
        ->and($credentials->host())->toBe('smtp.example.com');
});

it('creates SendGrid credentials', function () {
    $factory = new CredentialsFactory();

    $credentials = $factory->create(ProviderType::SENDGRID, [
        'api_key' => 'SG.test-key',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials)->toBeInstanceOf(SendGridCredentials::class)
        ->and($credentials->apiKey())->toBe('SG.test-key');
});

it('creates Mailgun credentials', function () {
    $factory = new CredentialsFactory();

    $credentials = $factory->create(ProviderType::MAILGUN, [
        'api_key' => 'key-test',
        'domain' => 'mg.example.com',
        'from_email' => 'test@mg.example.com',
    ]);

    expect($credentials)->toBeInstanceOf(MailgunCredentials::class)
        ->and($credentials->domain())->toBe('mg.example.com');
});

it('creates Postmark credentials', function () {
    $factory = new CredentialsFactory();

    $credentials = $factory->create(ProviderType::POSTMARK, [
        'server_token' => 'token-123',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials)->toBeInstanceOf(PostmarkCredentials::class)
        ->and($credentials->serverToken())->toBe('token-123');
});

it('creates SES credentials', function () {
    $factory = new CredentialsFactory();

    $credentials = $factory->create(ProviderType::SES, [
        'access_key_id' => 'AKIATEST',
        'secret_access_key' => 'secret',
        'region' => 'eu-west-1',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials)->toBeInstanceOf(SESCredentials::class)
        ->and($credentials->region())->toBe('eu-west-1');
});

it('throws exception for unsupported provider type', function () {
    $factory = new CredentialsFactory();

    $factory->create(ProviderType::TWILIO, [
        'account_sid' => 'AC123',
        'auth_token' => 'token',
    ]);
})->throws(UnsupportedProviderCredentialsException::class);

it('throws exception for SMS provider types', function (ProviderType $providerType) {
    $factory = new CredentialsFactory();

    $factory->create($providerType, ['some' => 'data']);
})->with([
    ProviderType::TWILIO,
    ProviderType::VONAGE,
    ProviderType::PLIVO,
])->throws(UnsupportedProviderCredentialsException::class);

it('throws exception for push provider types', function (ProviderType $providerType) {
    $factory = new CredentialsFactory();

    $factory->create($providerType, ['some' => 'data']);
})->with([
    ProviderType::FIREBASE,
    ProviderType::ONESIGNAL,
    ProviderType::PUSHER,
])->throws(UnsupportedProviderCredentialsException::class);
