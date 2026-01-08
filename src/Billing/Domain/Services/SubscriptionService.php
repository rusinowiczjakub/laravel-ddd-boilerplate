<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Services;

use DateTimeInterface;
use Modules\Shared\Domain\ValueObjects\Uuid;

/**
 * Domain service for subscription operations.
 * Infrastructure layer provides the actual implementation.
 */
interface SubscriptionService
{
    /**
     * Cancel subscription at period end.
     * Returns the date when cancellation takes effect.
     */
    public function cancelSubscription(Uuid $workspaceId): DateTimeInterface;

    /**
     * Schedule plan downgrade to take effect at period end.
     * Returns the date when the change takes effect.
     */
    public function scheduleDowngrade(Uuid $workspaceId, string $newPlan, string $billingPeriod): DateTimeInterface;
}
