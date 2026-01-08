<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Modules\Channels\Domain\Enums\ChannelType;
use Modules\Channels\Domain\Enums\ProviderType;
use Modules\Channels\Domain\Models\Provider;
use Modules\Channels\Domain\ValueObjects\Credentials\MailgunCredentials;
use Modules\Channels\Infrastructure\Senders\MailgunEmailSender;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;

function createMailgunProvider(string $endpoint = 'api.mailgun.net'): Provider
{
    return Provider::reconstitute(
        id: Id::create(),
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::MAILGUN,
        name: 'Mailgun Test',
        credentials: new MailgunCredentials(
            apiKey: 'key-test123',
            domain: 'mg.example.com',
            fromEmail: 'sender@mg.example.com',
            fromName: 'Sender',
            endpoint: $endpoint,
        ),
        isDefault: true,
        isActive: true,
        createdAt: Date::now(),
        updatedAt: Date::now(),
    );
}

it('supports only Mailgun provider type', function () {
    $sender = new MailgunEmailSender();

    expect($sender->supports(ProviderType::MAILGUN))->toBeTrue()
        ->and($sender->supports(ProviderType::SENDGRID))->toBeFalse()
        ->and($sender->supports(ProviderType::SMTP))->toBeFalse();
});

it('sends email successfully via Mailgun API', function () {
    Http::fake([
        'https://api.mailgun.net/v3/mg.example.com/messages' => Http::response([
            'id' => '<message-id@mailgun.org>',
            'message' => 'Queued. Thank you.',
        ], 200),
    ]);

    $sender = new MailgunEmailSender();
    $provider = createMailgunProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test Subject',
        body: 'Test body content',
    );

    expect($result->success)->toBeTrue()
        ->and($result->messageId)->toBe('<message-id@mailgun.org>');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.mailgun.net/v3/mg.example.com/messages')
            && $request['from'] === 'Sender <sender@mg.example.com>'
            && $request['to'] === 'recipient@example.com';
    });
});

it('uses EU endpoint when configured', function () {
    Http::fake([
        'https://api.eu.mailgun.net/*' => Http::response([
            'id' => '<eu-message-id@mailgun.org>',
        ], 200),
    ]);

    $sender = new MailgunEmailSender();
    $provider = createMailgunProvider('api.eu.mailgun.net');

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'api.eu.mailgun.net');
    });
});

it('sends email with HTML body', function () {
    Http::fake([
        'https://api.mailgun.net/*' => Http::response(['id' => 'msg-id'], 200),
    ]);

    $sender = new MailgunEmailSender();
    $provider = createMailgunProvider();

    $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Plain text',
        htmlBody: '<p>HTML content</p>',
    );

    Http::assertSent(function ($request) {
        return isset($request['html']) && $request['html'] === '<p>HTML content</p>';
    });
});

it('returns failed result on API error', function () {
    Http::fake([
        'https://api.mailgun.net/*' => Http::response([
            'message' => 'Forbidden',
        ], 403),
    ]);

    $sender = new MailgunEmailSender();
    $provider = createMailgunProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeFalse()
        ->and($result->error)->toBe('Forbidden');
});

it('formats from address without name when name is empty', function () {
    Http::fake([
        'https://api.mailgun.net/*' => Http::response(['id' => 'msg-id'], 200),
    ]);

    $provider = Provider::reconstitute(
        id: Id::create(),
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::MAILGUN,
        name: 'Mailgun',
        credentials: new MailgunCredentials(
            apiKey: 'key-test',
            domain: 'mg.example.com',
            fromEmail: 'noreply@mg.example.com',
            fromName: '', // Empty name
            endpoint: 'api.mailgun.net',
        ),
        isDefault: true,
        isActive: true,
        createdAt: Date::now(),
        updatedAt: Date::now(),
    );

    $sender = new MailgunEmailSender();
    $sender->send($provider, 'to@example.com', 'Subject', 'Body');

    Http::assertSent(function ($request) {
        return $request['from'] === 'noreply@mg.example.com';
    });
});
