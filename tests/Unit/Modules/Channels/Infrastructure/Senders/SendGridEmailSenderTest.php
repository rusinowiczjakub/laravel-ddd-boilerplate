<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Modules\Channels\Domain\Enums\ChannelType;
use Modules\Channels\Domain\Enums\ProviderType;
use Modules\Channels\Domain\Models\Provider;
use Modules\Channels\Domain\ValueObjects\Credentials\SendGridCredentials;
use Modules\Channels\Infrastructure\Senders\SendGridEmailSender;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;

function createSendGridProvider(): Provider
{
    return Provider::reconstitute(
        id: Id::create(),
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SENDGRID,
        name: 'SendGrid Test',
        credentials: new SendGridCredentials(
            apiKey: 'SG.test-api-key',
            fromEmail: 'sender@example.com',
            fromName: 'Sender Name',
        ),
        isDefault: true,
        isActive: true,
        createdAt: Date::now(),
        updatedAt: Date::now(),
    );
}

it('supports only SendGrid provider type', function () {
    $sender = new SendGridEmailSender();

    expect($sender->supports(ProviderType::SENDGRID))->toBeTrue()
        ->and($sender->supports(ProviderType::SMTP))->toBeFalse()
        ->and($sender->supports(ProviderType::MAILGUN))->toBeFalse();
});

it('sends email successfully via SendGrid API', function () {
    Http::fake([
        'https://api.sendgrid.com/v3/mail/send' => Http::response(null, 202, [
            'X-Message-Id' => 'msg-123',
        ]),
    ]);

    $sender = new SendGridEmailSender();
    $provider = createSendGridProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test Subject',
        body: 'Test body content',
    );

    expect($result->success)->toBeTrue()
        ->and($result->messageId)->toBe('msg-123')
        ->and($result->error)->toBeNull();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.sendgrid.com/v3/mail/send'
            && $request->hasHeader('Authorization', 'Bearer SG.test-api-key')
            && $request['from']['email'] === 'sender@example.com'
            && $request['personalizations'][0]['to'][0]['email'] === 'recipient@example.com'
            && $request['subject'] === 'Test Subject';
    });
});

it('sends email with HTML body', function () {
    Http::fake([
        'https://api.sendgrid.com/v3/mail/send' => Http::response(null, 202),
    ]);

    $sender = new SendGridEmailSender();
    $provider = createSendGridProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Plain text',
        htmlBody: '<html><body>HTML content</body></html>',
    );

    expect($result->success)->toBeTrue();

    Http::assertSent(function ($request) {
        $content = $request['content'];
        return count($content) === 2
            && $content[0]['type'] === 'text/plain'
            && $content[1]['type'] === 'text/html';
    });
});

it('returns failed result on API error', function () {
    Http::fake([
        'https://api.sendgrid.com/v3/mail/send' => Http::response([
            'errors' => [
                ['message' => 'Invalid API key'],
            ],
        ], 401),
    ]);

    $sender = new SendGridEmailSender();
    $provider = createSendGridProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeFalse()
        ->and($result->error)->toBe('Invalid API key');
});

it('returns failed result on connection error', function () {
    Http::fake([
        'https://api.sendgrid.com/v3/mail/send' => function () {
            throw new \Exception('Connection timeout');
        },
    ]);

    $sender = new SendGridEmailSender();
    $provider = createSendGridProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeFalse()
        ->and($result->error)->toContain('Connection timeout');
});
