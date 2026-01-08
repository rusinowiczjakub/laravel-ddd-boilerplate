<?php

declare(strict_types=1);

namespace Tests\Stub;

use Modules\Core\Events\Contracts\Event;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Shared\Domain\ValueObjects\Uuid;

class TestEventStub implements Event
{
    public function eventId(): Uuid
    {
        return Uuid::create();
    }

    public function aggregateId(): string
    {
        return Id::create()->value();
    }

    public function aggregateType(): string
    {
        return 'test';
    }

    public function occurredAt(): Date
    {
        return new Date;
    }

    public function toPayload(): array
    {
        return [];
    }

    public static function fromPayload(array $payload): Event
    {
        return new self;
    }

    public function aggregateVersion(): int
    {
        return 1;
    }

    public function withAggregateVersion(int $aggregateVersion): Event
    {
        return $this;
    }
}
