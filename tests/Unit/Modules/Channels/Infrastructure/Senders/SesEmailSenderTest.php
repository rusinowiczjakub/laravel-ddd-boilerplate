<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Modules\Channels\Domain\Enums\ChannelType;
use Modules\Channels\Domain\Enums\ProviderType;
use Modules\Channels\Domain\Models\Provider;
use Modules\Channels\Domain\ValueObjects\Credentials\SESCredentials;
use Modules\Channels\Infrastructure\Senders\SesEmailSender;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;

function createSesProvider(string $region = 'us-east-1'): Provider
{
    return Provider::reconstitute(
        id: Id::create(),
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SES,
        name: 'SES Test',
        credentials: new SESCredentials(
            accessKeyId: 'AKIAIOSFODNN7EXAMPLE',
            secretAccessKey: 'wJalrXUtnFEMI/K7MDENG/bPxRfiCYEXAMPLEKEY',
            region: $region,
            fromEmail: 'sender@example.com',
            fromName: 'Sender Name',
        ),
        isDefault: true,
        isActive: true,
        createdAt: Date::now(),
        updatedAt: Date::now(),
    );
}

it('supports only SES provider type', function () {
    $sender = new SesEmailSender();

    expect($sender->supports(ProviderType::SES))->toBeTrue()
        ->and($sender->supports(ProviderType::SENDGRID))->toBeFalse()
        ->and($sender->supports(ProviderType::SMTP))->toBeFalse();
});

it('sends email successfully via SES API', function () {
    Http::fake([
        'https://email.us-east-1.amazonaws.com/*' => Http::response([
            'MessageId' => 'ses-message-id-12345',
        ], 200),
    ]);

    $sender = new SesEmailSender();
    $provider = createSesProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test Subject',
        body: 'Test body content',
    );

    expect($result->success)->toBeTrue()
        ->and($result->messageId)->toBe('ses-message-id-12345');

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'email.us-east-1.amazonaws.com')
            && $request->hasHeader('Authorization')
            && $request->hasHeader('X-Amz-Date');
    });
});

it('uses correct regional endpoint', function () {
    Http::fake([
        'https://email.eu-west-1.amazonaws.com/*' => Http::response([
            'MessageId' => 'eu-message-id',
        ], 200),
    ]);

    $sender = new SesEmailSender();
    $provider = createSesProvider('eu-west-1');

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeTrue();

    Http::assertSent(function ($request) {
        return str_contains($request->url(), 'email.eu-west-1.amazonaws.com');
    });
});

it('includes AWS Signature V4 authorization header', function () {
    Http::fake([
        'https://email.us-east-1.amazonaws.com/*' => Http::response([
            'MessageId' => 'msg-id',
        ], 200),
    ]);

    $sender = new SesEmailSender();
    $provider = createSesProvider();

    $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    Http::assertSent(function ($request) {
        $auth = $request->header('Authorization')[0] ?? '';
        return str_contains($auth, 'AWS4-HMAC-SHA256')
            && str_contains($auth, 'Credential=AKIAIOSFODNN7EXAMPLE')
            && str_contains($auth, 'SignedHeaders=')
            && str_contains($auth, 'Signature=');
    });
});

it('sends email with HTML body', function () {
    Http::fake([
        'https://email.us-east-1.amazonaws.com/*' => Http::response([
            'MessageId' => 'msg-id',
        ], 200),
    ]);

    $sender = new SesEmailSender();
    $provider = createSesProvider();

    $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Plain text',
        htmlBody: '<p>HTML content</p>',
    );

    Http::assertSent(function ($request) {
        $body = json_decode($request->body(), true);
        return isset($body['Content']['Simple']['Body']['Html']);
    });
});

it('returns failed result on API error', function () {
    Http::fake([
        'https://email.us-east-1.amazonaws.com/*' => Http::response([
            'message' => 'The security token included in the request is invalid.',
        ], 403),
    ]);

    $sender = new SesEmailSender();
    $provider = createSesProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeFalse()
        ->and($result->error)->toContain('security token');
});

it('returns failed result on connection error', function () {
    Http::fake([
        'https://email.us-east-1.amazonaws.com/*' => function () {
            throw new \Exception('Could not resolve host');
        },
    ]);

    $sender = new SesEmailSender();
    $provider = createSesProvider();

    $result = $sender->send(
        provider: $provider,
        to: 'recipient@example.com',
        subject: 'Test',
        body: 'Test body',
    );

    expect($result->success)->toBeFalse()
        ->and($result->error)->toContain('Could not resolve host');
});
