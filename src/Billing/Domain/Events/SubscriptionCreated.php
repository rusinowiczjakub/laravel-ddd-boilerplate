<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Events;


use Modules\Core\Events\Contracts\Event;
use Modules\Core\Events\DomainEvent;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;

/**
 * SubscriptionCreated - Domain event emitted when Stripe subscription is created.
 *
 * This event is emitted by the webhook handler when customer.subscription.created
 * webhook is received from Stripe.
 */
final class SubscriptionCreated extends DomainEvent implements IntegrationEvent
{
    public function __construct(
        public string $workspaceId,
        public string $subscriptionId,
        public string $plan,
        public string $status,
        public string $stripePriceId,
        public ?int $currentPeriodStart = null,
        public ?int $currentPeriodEnd = null,
    ) {
        parent::__construct($this->workspaceId, 'workspace');
    }

    public function key(): string
    {
        return 'billing.subscription.created';
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->workspaceId,
            'subscription_id' => $this->subscriptionId,
            'plan' => $this->plan,
            'status' => $this->status,
            'stripe_price_id' => $this->stripePriceId,
            'current_period_start' => $this->currentPeriodStart,
            'current_period_end' => $this->currentPeriodEnd,
        ];
    }

    public static function fromPayload(array $payload): \Modules\Core\Events\Contracts\Event
    {
        return new self(
            workspaceId: $payload['workspace_id'],
            subscriptionId: $payload['subscription_id'],
            plan: $payload['plan'],
            status: $payload['status'],
            stripePriceId: $payload['stripe_price_id'],
            currentPeriodStart: $payload['current_period_start'] ?? null,
            currentPeriodEnd: $payload['current_period_end'] ?? null,
        );
    }
}
