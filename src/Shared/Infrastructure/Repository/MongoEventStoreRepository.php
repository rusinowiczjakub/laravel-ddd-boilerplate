<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure\Repository;

use Modules\Core\Events\Contracts\Event;
use Modules\Shared\Domain\Repositories\EventStoreRepository;
use Modules\Shared\Infrastructure\Models\EventStore;

class MongoEventStoreRepository implements EventStoreRepository
{
    public function store(Event $event): void
    {
        EventStore::create([
            'event_id' => $event->eventId()->value(),
            'aggregate_id' => $event->aggregateId(),
            'aggregate_type' => $event->aggregateType(),
            'aggregate_version' => $event->aggregateVersion(),
            'occurred_at' => $event->occurredAt(),
            'payload' => $event->toPayload(),
            'event' => get_class($event),
        ]);
    }
}
