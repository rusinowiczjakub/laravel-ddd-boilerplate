<?php

declare(strict_types=1);

use Modules\Channels\Domain\Enums\Encryption;
use Modules\Channels\Domain\ValueObjects\Credentials\SMTPCredentials;

it('creates SMTP credentials with all fields', function () {
    $credentials = new SMTPCredentials(
        host: 'smtp.example.com',
        port: '587',
        username: 'user@example.com',
        password: 'secret123',
        encryption: Encryption::TLS,
        fromEmail: 'noreply@example.com',
        fromName: 'My App',
    );

    expect($credentials->host())->toBe('smtp.example.com')
        ->and($credentials->port())->toBe('587')
        ->and($credentials->username())->toBe('user@example.com')
        ->and($credentials->password())->toBe('secret123')
        ->and($credentials->encryption())->toBe(Encryption::TLS)
        ->and($credentials->fromEmail())->toBe('noreply@example.com')
        ->and($credentials->fromName())->toBe('My App');
});

it('creates SMTP credentials from array', function () {
    $data = [
        'host' => 'smtp.gmail.com',
        'port' => '465',
        'username' => 'test@gmail.com',
        'password' => 'app-password',
        'encryption' => 'ssl',
        'from_email' => 'sender@gmail.com',
        'from_name' => 'Sender Name',
    ];

    $credentials = SMTPCredentials::fromArray($data);

    expect($credentials->host())->toBe('smtp.gmail.com')
        ->and($credentials->port())->toBe('465')
        ->and($credentials->encryption())->toBe(Encryption::SSL)
        ->and($credentials->fromEmail())->toBe('sender@gmail.com')
        ->and($credentials->fromName())->toBe('Sender Name');
});

it('converts SMTP credentials to array', function () {
    $credentials = new SMTPCredentials(
        host: 'smtp.example.com',
        port: '587',
        username: 'user',
        password: 'pass',
        encryption: Encryption::TLS,
        fromEmail: 'from@example.com',
        fromName: 'From Name',
    );

    $array = $credentials->toArray();

    expect($array)->toBe([
        'host' => 'smtp.example.com',
        'port' => '587',
        'username' => 'user',
        'password' => 'pass',
        'encryption' => 'tls',
        'from_email' => 'from@example.com',
        'from_name' => 'From Name',
    ]);
});

it('defaults to NONE encryption when invalid value provided', function () {
    $credentials = SMTPCredentials::fromArray([
        'host' => 'smtp.example.com',
        'port' => '25',
        'username' => 'user',
        'password' => 'pass',
        'encryption' => 'invalid',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials->encryption())->toBe(Encryption::NONE);
});

it('defaults to empty from_name when not provided', function () {
    $credentials = SMTPCredentials::fromArray([
        'host' => 'smtp.example.com',
        'port' => '587',
        'username' => 'user',
        'password' => 'pass',
        'encryption' => 'tls',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials->fromName())->toBe('');
});
