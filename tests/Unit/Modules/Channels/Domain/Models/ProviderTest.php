<?php

declare(strict_types=1);

use Modules\Channels\Domain\Enums\ChannelType;
use Modules\Channels\Domain\Enums\ProviderType;
use Modules\Channels\Domain\Events\ProviderCreated;
use Modules\Channels\Domain\Events\ProviderDeleted;
use Modules\Channels\Domain\Events\ProviderUpdated;
use Modules\Channels\Domain\Models\Provider;
use Modules\Channels\Domain\ValueObjects\Credentials\SMTPCredentials;
use Modules\Channels\Domain\Enums\Encryption;
use Modules\Shared\Domain\ValueObjects\Id;

function createTestCredentials(): SMTPCredentials
{
    return new SMTPCredentials(
        host: 'smtp.example.com',
        port: '587',
        username: 'user',
        password: 'pass',
        encryption: Encryption::TLS,
        fromEmail: 'test@example.com',
        fromName: 'Test',
    );
}

it('creates a provider with correct data', function () {
    $workspaceId = Id::create();
    $credentials = createTestCredentials();

    $provider = Provider::create(
        workspaceId: $workspaceId,
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SMTP,
        name: 'My SMTP Provider',
        credentials: $credentials,
        isDefault: false,
    );

    expect($provider->id())->toBeInstanceOf(Id::class)
        ->and($provider->workspaceId()->value())->toBe($workspaceId->value())
        ->and($provider->channelType())->toBe(ChannelType::EMAIL)
        ->and($provider->provider())->toBe(ProviderType::SMTP)
        ->and($provider->name())->toBe('My SMTP Provider')
        ->and($provider->credentials())->toBe($credentials)
        ->and($provider->isDefault())->toBeFalse()
        ->and($provider->isActive())->toBeTrue();
});

it('records ProviderCreated event when created', function () {
    $provider = Provider::create(
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SMTP,
        name: 'Test Provider',
        credentials: createTestCredentials(),
    );

    $events = $provider->pullEvents();

    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(ProviderCreated::class)
        ->and($events[0]->providerId)->toBe($provider->id()->value())
        ->and($events[0]->channelType)->toBe(ChannelType::EMAIL->value);
});

it('creates provider as default when specified', function () {
    $provider = Provider::create(
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SENDGRID,
        name: 'Default Provider',
        credentials: createTestCredentials(),
        isDefault: true,
    );

    expect($provider->isDefault())->toBeTrue();
});

it('updates provider name and credentials', function () {
    $provider = Provider::create(
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SMTP,
        name: 'Original Name',
        credentials: createTestCredentials(),
    );

    $provider->pullEvents(); // Clear creation event

    $newCredentials = new SMTPCredentials(
        host: 'new.smtp.com',
        port: '465',
        username: 'newuser',
        password: 'newpass',
        encryption: Encryption::SSL,
        fromEmail: 'new@example.com',
        fromName: 'New Name',
    );

    $provider->update('Updated Name', $newCredentials);

    expect($provider->name())->toBe('Updated Name')
        ->and($provider->credentials()->host())->toBe('new.smtp.com');

    $events = $provider->pullEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(ProviderUpdated::class);
});

it('marks provider as default', function () {
    $provider = Provider::create(
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SMTP,
        name: 'Provider',
        credentials: createTestCredentials(),
        isDefault: false,
    );

    $provider->pullEvents();

    $provider->markAsDefault();

    expect($provider->isDefault())->toBeTrue();

    $events = $provider->pullEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(ProviderUpdated::class)
        ->and($events[0]->isDefault)->toBeTrue();
});

it('unmarks provider as default without event', function () {
    $provider = Provider::create(
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SMTP,
        name: 'Provider',
        credentials: createTestCredentials(),
        isDefault: true,
    );

    $provider->pullEvents();

    $provider->unmarkAsDefault();

    expect($provider->isDefault())->toBeFalse();

    // unmarkAsDefault should not emit event (it's internal operation)
    $events = $provider->pullEvents();
    expect($events)->toHaveCount(0);
});

it('records ProviderDeleted event when deleted', function () {
    $provider = Provider::create(
        workspaceId: Id::create(),
        channelType: ChannelType::EMAIL,
        provider: ProviderType::SMTP,
        name: 'Provider',
        credentials: createTestCredentials(),
    );

    $provider->pullEvents();

    $provider->delete();

    $events = $provider->pullEvents();
    expect($events)->toHaveCount(1)
        ->and($events[0])->toBeInstanceOf(ProviderDeleted::class)
        ->and($events[0]->providerId)->toBe($provider->id()->value());
});

it('reconstitutes provider from persisted data', function () {
    $id = Id::create();
    $workspaceId = Id::create();
    $credentials = createTestCredentials();
    $createdAt = new \Modules\Shared\Domain\ValueObjects\Date(new \DateTimeImmutable('2024-01-01'));
    $updatedAt = new \Modules\Shared\Domain\ValueObjects\Date(new \DateTimeImmutable('2024-01-02'));

    $provider = Provider::reconstitute(
        id: $id,
        workspaceId: $workspaceId,
        channelType: ChannelType::EMAIL,
        provider: ProviderType::MAILGUN,
        name: 'Reconstituted Provider',
        credentials: $credentials,
        isDefault: true,
        isActive: true,
        createdAt: $createdAt,
        updatedAt: $updatedAt,
    );

    expect($provider->id()->value())->toBe($id->value())
        ->and($provider->name())->toBe('Reconstituted Provider')
        ->and($provider->provider())->toBe(ProviderType::MAILGUN)
        ->and($provider->isDefault())->toBeTrue()
        ->and($provider->createdAt()->toDateTimeString())->toBe('2024-01-01 00:00:00');

    // Reconstitute should not emit events
    $events = $provider->pullEvents();
    expect($events)->toHaveCount(0);
});
