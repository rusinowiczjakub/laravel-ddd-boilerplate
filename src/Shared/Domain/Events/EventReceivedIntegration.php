<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Events\Domain\Event;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;

/**
 * Integration event emitted when a client event is received.
 *
 * This is the cross-boundary event that other modules (like Workflows)
 * can subscribe to without coupling to the Events domain.
 */
final class EventReceivedIntegration extends DomainEvent implements IntegrationEvent
{
    public function __construct(
        public string $id,
        public string $workspaceId,
        public string $eventName,
        public ?string $userId,
        public array $context,
        public int $receivedAt,
        public bool $isTest,
    ) {
        parent::__construct($id, Event::class);
    }

    public function key(): string
    {
        return 'event.received';
    }

    public function toPayload(): array
    {
        return [
            'event_id' => $this->id,
            'workspace_id' => $this->workspaceId,
            'event_name' => $this->eventName,
            'user_id' => $this->userId,
            'context' => $this->context,
            'received_at' => $this->receivedAt,
            'is_test' => $this->isTest,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            id: $payload['event_id'],
            workspaceId: $payload['workspace_id'],
            eventName: $payload['event_name'],
            userId: $payload['user_id'],
            context: $payload['context'],
            receivedAt: $payload['received_at'],
            isTest: $payload['is_test'] ?? false,
        );
    }
}
