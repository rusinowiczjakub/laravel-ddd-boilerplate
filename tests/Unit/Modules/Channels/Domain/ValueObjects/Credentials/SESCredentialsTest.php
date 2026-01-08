<?php

declare(strict_types=1);

use Modules\Channels\Domain\ValueObjects\Credentials\SESCredentials;

it('creates SES credentials with all fields', function () {
    $credentials = new SESCredentials(
        accessKeyId: 'AKIAIOSFODNN7EXAMPLE',
        secretAccessKey: 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
        region: 'eu-west-1',
        fromEmail: 'noreply@example.com',
        fromName: 'My App',
    );

    expect($credentials->accessKeyId())->toBe('AKIAIOSFODNN7EXAMPLE')
        ->and($credentials->secretAccessKey())->toBe('wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY')
        ->and($credentials->region())->toBe('eu-west-1')
        ->and($credentials->fromEmail())->toBe('noreply@example.com')
        ->and($credentials->fromName())->toBe('My App');
});

it('creates SES credentials from array', function () {
    $data = [
        'access_key_id' => 'AKIA123456789',
        'secret_access_key' => 'secret/key/here',
        'region' => 'us-west-2',
        'from_email' => 'sender@example.com',
        'from_name' => 'Sender',
    ];

    $credentials = SESCredentials::fromArray($data);

    expect($credentials->accessKeyId())->toBe('AKIA123456789')
        ->and($credentials->secretAccessKey())->toBe('secret/key/here')
        ->and($credentials->region())->toBe('us-west-2')
        ->and($credentials->fromEmail())->toBe('sender@example.com');
});

it('converts SES credentials to array', function () {
    $credentials = new SESCredentials(
        accessKeyId: 'AKIATEST',
        secretAccessKey: 'secrettest',
        region: 'eu-central-1',
        fromEmail: 'from@example.com',
        fromName: 'From',
    );

    expect($credentials->toArray())->toBe([
        'access_key_id' => 'AKIATEST',
        'secret_access_key' => 'secrettest',
        'region' => 'eu-central-1',
        'from_email' => 'from@example.com',
        'from_name' => 'From',
    ]);
});

it('defaults to us-east-1 region when not provided', function () {
    $credentials = SESCredentials::fromArray([
        'access_key_id' => 'AKIATEST',
        'secret_access_key' => 'secret',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials->region())->toBe('us-east-1');
});

it('defaults to empty from_name when not provided', function () {
    $credentials = SESCredentials::fromArray([
        'access_key_id' => 'AKIATEST',
        'secret_access_key' => 'secret',
        'region' => 'us-east-1',
        'from_email' => 'test@example.com',
    ]);

    expect($credentials->fromName())->toBe('');
});
