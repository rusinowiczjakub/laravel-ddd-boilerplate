<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;

/**
 * SubscriptionUpdated - Domain event emitted when Stripe subscription is updated.
 *
 * This event is emitted by the webhook handler when customer.subscription.updated
 * webhook is received from Stripe (renewals, cancellations, plan changes).
 */
final class SubscriptionUpdated extends DomainEvent implements IntegrationEvent
{
    public function __construct(
        public string $workspaceId,
        public string $subscriptionId,
        public string $status,
        public string $stripePriceId,
        public ?int $currentPeriodStart = null,
        public ?int $currentPeriodEnd = null,
        public bool $cancelAtPeriodEnd = false,
        public ?int $canceledAt = null,
    ) {
        parent::__construct($this->workspaceId, 'workspace');
    }

    public function key(): string
    {
        return 'billing.subscription.updated';
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->workspaceId,
            'subscription_id' => $this->subscriptionId,
            'status' => $this->status,
            'stripe_price_id' => $this->stripePriceId,
            'current_period_start' => $this->currentPeriodStart,
            'current_period_end' => $this->currentPeriodEnd,
            'cancel_at_period_end' => $this->cancelAtPeriodEnd,
            'canceled_at' => $this->canceledAt,
        ];
    }

    public static function fromPayload(array $payload): \Modules\Core\Events\Contracts\Event
    {
        return new self(
            workspaceId: $payload['workspace_id'],
            subscriptionId: $payload['subscription_id'],
            status: $payload['status'],
            stripePriceId: $payload['stripe_price_id'],
            currentPeriodStart: $payload['current_period_start'] ?? null,
            currentPeriodEnd: $payload['current_period_end'] ?? null,
            cancelAtPeriodEnd: $payload['cancel_at_period_end'] ?? false,
            canceledAt: $payload['canceled_at'] ?? null,
        );
    }
}
