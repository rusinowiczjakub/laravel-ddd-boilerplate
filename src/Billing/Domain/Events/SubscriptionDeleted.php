<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;

/**
 * SubscriptionDeleted - Domain event emitted when Stripe subscription ends.
 *
 * This event is emitted by the webhook handler when customer.subscription.deleted
 * webhook is received from Stripe (subscription period ended after cancellation).
 */
final class SubscriptionDeleted extends DomainEvent implements IntegrationEvent
{
    public function __construct(
        public string $workspaceId,
        public string $subscriptionId,
    ) {
        parent::__construct($this->workspaceId, 'workspace');
    }

    public function key(): string
    {
        return 'billing.subscription.deleted';
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->workspaceId,
            'subscription_id' => $this->subscriptionId,
        ];
    }

    public static function fromPayload(array $payload): \Modules\Core\Events\Contracts\Event
    {
        return new self(
            workspaceId: $payload['workspace_id'],
            subscriptionId: $payload['subscription_id'],
        );
    }
}
