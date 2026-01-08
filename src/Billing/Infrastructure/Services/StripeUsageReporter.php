<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Services;

use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;

/**
 * Reports usage to Stripe metered billing.
 */
final readonly class StripeUsageReporter
{
    /**
     * Report overage event to Stripe.
     * Stripe will aggregate usage and charge at end of billing period.
     */
    public function reportEventOverage(WorkspaceId $workspaceId, int $quantity = 1): void
    {
        $workspace = WorkspaceModel::find($workspaceId->value());

        if (!$workspace || !$workspace->subscribed()) {
            return;
        }

        try {
            // Report usage to Stripe metered billing
            // The price must be configured in Stripe as metered price with ID 'events_overage'
            $workspace->subscription()->reportUsageFor(
                'events_overage',
                $quantity
            );
        } catch (\Exception $e) {
            // Log but don't fail - usage reporting is best-effort
            logger()->warning('Failed to report usage to Stripe', [
                'workspace_id' => $workspaceId->value(),
                'quantity' => $quantity,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
