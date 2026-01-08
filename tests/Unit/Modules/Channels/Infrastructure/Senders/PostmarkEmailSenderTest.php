<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Modules\Channels\Domain\Enums\ChannelType;
use Modules\Channels\Domain\Enums\ProviderType;
use Modules\Channels\Domain\Models\Provider;
use Modules\Channels\Domain\ValueObjects\Credentials\PostmarkCredentials;
use Modules\Channels\Infrastructure\Senders\PostmarkEmailSender;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;

function createPostmarkProvider(): Provider
{
    return Provider::reconstitute(
        id: Id::create(),
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::POSTMARK,
        name: 'Postmark Test',
        credentials: new PostmarkCredentials(
            serverToken: 'test-server-token',
            fromEmail: 'sender@example.com',
            fromName: 'Sender Name',
        ),
        isDefault: true,
        isActive: true,
        createdAt: Date::now(),
        updatedAt: Date::now(),
    );
}

it('supports only Postmark provider type', function () {
    $sender = new PostmarkEmailSender();

    expect($sender->supports(ProviderType::POSTMARK))->toBeTrue()
        ->and($sender->supports(ProviderType::SENDGRID))->toBeFalse()
        ->and($sender->supports(ProviderType::MAILGUN))->toBeFalse();
});

it('sends email successfully via Postmark API', function () {
    Http::fake([
        'https://api.postmarkapp.com/email' => Http::response([
            'MessageID' => 'b7bc2f4a-e38e-4336-af7d-e6c392c2f817',
            'SubmittedAt' => '2024-01-01T00:00:00.0000000Z',
            'To' => 'recipient@example.com',
        ], 200),
    ]);

    $sender = new PostmarkEmailSender();
    $provider = createPostmarkProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test Subject',
        body: 'Test body content',
    );

    expect($result->success)->toBeTrue()
        ->and($result->messageId)->toBe('b7bc2f4a-e38e-4336-af7d-e6c392c2f817');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.postmarkapp.com/email'
            && $request->hasHeader('X-Postmark-Server-Token', 'test-server-token')
            && $request['From'] === 'Sender Name <sender@example.com>'
            && $request['To'] === 'recipient@example.com'
            && $request['Subject'] === 'Test Subject'
            && $request['TextBody'] === 'Test body content';
    });
});

it('sends email with HTML body', function () {
    Http::fake([
        'https://api.postmarkapp.com/email' => Http::response([
            'MessageID' => 'msg-id',
        ], 200),
    ]);

    $sender = new PostmarkEmailSender();
    $provider = createPostmarkProvider();

    $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Plain text',
        htmlBody: '<p>HTML content</p>',
    );

    Http::assertSent(function ($request) {
        return $request['HtmlBody'] === '<p>HTML content</p>';
    });
});

it('returns failed result on API error', function () {
    Http::fake([
        'https://api.postmarkapp.com/email' => Http::response([
            'ErrorCode' => 10,
            'Message' => 'Bad or missing Server API token.',
        ], 401),
    ]);

    $sender = new PostmarkEmailSender();
    $provider = createPostmarkProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeFalse()
        ->and($result->error)->toBe('Bad or missing Server API token.');
});

it('returns failed result on inactive sender signature', function () {
    Http::fake([
        'https://api.postmarkapp.com/email' => Http::response([
            'ErrorCode' => 400,
            'Message' => 'Sender signature not found.',
        ], 422),
    ]);

    $sender = new PostmarkEmailSender();
    $provider = createPostmarkProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeFalse()
        ->and($result->error)->toContain('Sender signature');
});
