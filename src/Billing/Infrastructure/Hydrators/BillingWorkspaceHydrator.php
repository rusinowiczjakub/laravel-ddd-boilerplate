<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Hydrators;

use Modules\Billing\Domain\Enums\Plan;
use Modules\Billing\Domain\Models\BillingWorkspace;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;
use Modules\Shared\Domain\ValueObjects\Id;

final readonly class BillingWorkspaceHydrator
{
    /**
     * Convert infrastructure model to domain model.
     */
    public function toDomain(WorkspaceModel $model): BillingWorkspace
    {
        return BillingWorkspace::reconstitute([
            'id' => new Id($model->id),
            'name' => $model->name,
            'plan' => Plan::from($model->plan),
            'pendingPlan' => $model->pending_plan,
            'pendingBillingPeriod' => $model->pending_billing_period,
            'planChangesAt' => $model->plan_changes_at,
            'stripeCustomerId' => $model->stripe_id,
        ]);
    }

    /**
     * Sync domain model changes to infrastructure model.
     */
    public function syncToModel(BillingWorkspace $workspace, WorkspaceModel $model): void
    {
        $model->plan = $workspace->plan()->value;
        $model->pending_plan = $workspace->pendingPlan();
        $model->pending_billing_period = $workspace->pendingBillingPeriod();
        $model->plan_changes_at = $workspace->planChangesAt();
    }
}
