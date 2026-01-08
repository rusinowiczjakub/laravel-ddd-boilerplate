<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Events;

use Modules\Core\Events\DomainEvent;

final class PlanDowngradeScheduled extends DomainEvent
{
    public function __construct(
        string $workspaceId,
        public readonly string $currentPlan,
        public readonly string $newPlan,
        public readonly string $billingPeriod,
    ) {
        parent::__construct($workspaceId, 'billing_workspace');
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->aggregateId(),
            'current_plan' => $this->currentPlan,
            'new_plan' => $this->newPlan,
            'billing_period' => $this->billingPeriod,
        ];
    }

    public static function fromPayload(array $payload): self
    {
        return new self(
            $payload['workspace_id'],
            $payload['current_plan'],
            $payload['new_plan'],
            $payload['billing_period'],
        );
    }
}
