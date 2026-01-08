<?php

namespace Modules\Shared\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;
use Modules\Workspaces\Domain\Models\Workspace;

class EventLimitWarningIntegrationEvent extends DomainEvent implements IntegrationEvent
{
    public function __construct(
        public string $workspaceId,
        public int $currentUsage,
        public int $limit,
        public float $percentageUsed,
    ) {
        parent::__construct($this->workspaceId, Workspace::class);
    }

    public function key(): string
    {
        return 'events.limit_warning';
    }

    public static function fromPayload(array $payload): static
    {
        return new self(
            $payload['workspace_id'],
            $payload['current_usage'],
            $payload['limit'],
            $payload['percentage_used'],
        );
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->workspaceId,
            'current_usage' => $this->currentUsage,
            'limit' => $this->limit,
            'percentage_used' => $this->percentageUsed,
        ];
    }
}
