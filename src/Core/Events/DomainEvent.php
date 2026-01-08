<?php

declare(strict_types=1);

namespace Modules\Core\Events;

use Modules\Core\Events\Contracts\Event;
use Modules\Shared\Domain\Exceptions\InvalidUuidException;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Uuid;

abstract class DomainEvent implements Event
{
    protected readonly string $aggregateId;

    protected readonly string $aggregateType;

    protected ?int $aggregateVersion = 1;

    protected readonly Uuid $eventId;

    protected readonly Date $occurredAt;

    /**
     * @throws InvalidUuidException
     */
    public function __construct(string $aggregateId, string $aggregateType)
    {
        $this->aggregateId = $aggregateId;
        $this->aggregateType = $aggregateType;

        $this->eventId = Uuid::create();
        $this->occurredAt = new Date();
    }

    public function eventId(): Uuid
    {
        return $this->eventId;
    }

    public function aggregateId(): string
    {
        return $this->aggregateId;
    }

    public function aggregateType(): string
    {
        return $this->aggregateType;
    }

    public function occurredAt(): Date
    {
        return $this->occurredAt;
    }

    public function aggregateVersion(): ?int
    {
        return $this->aggregateVersion;
    }

    public function withAggregateVersion(int $version): static
    {
        $this->aggregateVersion = $version;

        return $this;
    }
}
