<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Events;

use Modules\Core\Events\DomainEvent;

final class SubscriptionCancellationRequested extends DomainEvent
{
    public function __construct(
        string $workspaceId,
    ) {
        parent::__construct($workspaceId, 'billing_workspace');
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->aggregateId(),
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['workspace_id'],
        );
    }
}
