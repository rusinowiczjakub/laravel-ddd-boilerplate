<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Models;

use DateTimeInterface;
use Modules\Billing\Domain\Enums\Plan;
use Modules\Billing\Domain\Events\PlanDowngradeScheduled;
use Modules\Billing\Domain\Events\SubscriptionCancellationRequested;
use Modules\Core\Aggregate\AggregateRoot;
use Modules\Shared\Domain\Exceptions\DomainException;
use Modules\Shared\Domain\ValueObjects\Uuid;

/**
 * BillingWorkspace - Pure domain aggregate for workspace billing operations.
 */
final class BillingWorkspace extends AggregateRoot
{
    private function __construct(
        private Uuid $id,
        private string $name,
        private Plan $plan,
        private ?string $pendingPlan,
        private ?string $pendingBillingPeriod,
        private ?DateTimeInterface $planChangesAt,
        private ?string $stripeCustomerId,
    ) {}

    public static function reconstitute(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            plan: $data['plan'],
            pendingPlan: $data['pendingPlan'] ?? null,
            pendingBillingPeriod: $data['pendingBillingPeriod'] ?? null,
            planChangesAt: $data['planChangesAt'] ?? null,
            stripeCustomerId: $data['stripeCustomerId'] ?? null,
        );
    }

    public function cancelSubscription(): void
    {
        // Business rule: can only cancel if has Stripe customer
        if (!$this->stripeCustomerId) {
            throw new DomainException('Workspace has no Stripe customer');
        }

        // Emit event - infrastructure will handle actual Stripe cancellation
        $this->record(new SubscriptionCancellationRequested(
            workspaceId: $this->id->value(),
        ));
    }

    public function schedulePlanDowngrade(Plan $newPlan, string $billingPeriod): void
    {
        // Business rules
        if (!$this->stripeCustomerId) {
            throw new DomainException('Workspace has no Stripe customer');
        }

        if ($newPlan === $this->plan) {
            throw new DomainException('Already on this plan');
        }

        // Emit event - infrastructure will handle Stripe API call and set planChangesAt
        $this->record(new PlanDowngradeScheduled(
            workspaceId: $this->id->value(),
            currentPlan: $this->plan->value,
            newPlan: $newPlan->value,
            billingPeriod: $billingPeriod,
        ));
    }

    public function setPendingPlanChange(string $plan, ?string $billingPeriod, ?DateTimeInterface $changesAt): void
    {
        $this->pendingPlan = $plan;
        $this->pendingBillingPeriod = $billingPeriod;
        $this->planChangesAt = $changesAt;
    }

    public function clearPendingPlanChange(): void
    {
        $this->pendingPlan = null;
        $this->pendingBillingPeriod = null;
        $this->planChangesAt = null;
    }

    public function changePlan(Plan $plan): void
    {
        $this->plan = $plan;
    }

    // Getters
    public function id(): Uuid
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function plan(): Plan
    {
        return $this->plan;
    }

    public function pendingPlan(): ?string
    {
        return $this->pendingPlan;
    }

    public function pendingBillingPeriod(): ?string
    {
        return $this->pendingBillingPeriod;
    }

    public function planChangesAt(): ?DateTimeInterface
    {
        return $this->planChangesAt;
    }

    public function stripeCustomerId(): ?string
    {
        return $this->stripeCustomerId;
    }

    public function hasStripeCustomer(): bool
    {
        return $this->stripeCustomerId !== null;
    }
}
