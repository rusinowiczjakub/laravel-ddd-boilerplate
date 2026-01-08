<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Billing\Application\Responses\ChangePlanResponse;
use Modules\Billing\Domain\Enums\Plan;
use Modules\Billing\Domain\Repositories\BillingWorkspaceRepository;
use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Core\Events\Contracts\EventBus;
use Modules\Shared\Domain\Exceptions\DomainException;

#[CommandHandler(ChangePlanCommand::class)]
final readonly class ChangePlanHandler
{
    public function __construct(
        private BillingWorkspaceRepository $workspaceRepository,
        private CommandBus $commandBus,
        private EventBus $eventBus,
    ) {}

    public function handle(ChangePlanCommand $command): ChangePlanResponse
    {
        // 1. Load workspace
        $workspace = $this->workspaceRepository->findById($command->workspaceId);

        if (!$workspace) {
            throw new DomainException('Workspace not found');
        }

        $currentPlan = $workspace->plan();
        $newPlan = Plan::from($command->newPlan);

        // 2. Check if same plan
        if ($currentPlan === $newPlan) {
            throw new DomainException('Already on this plan');
        }

        // 3. Determine action based on plan change
        $isUpgrade = $this->isUpgrade($currentPlan, $newPlan);

        // 4. Handle upgrade - redirect to checkout
        if ($isUpgrade) {
            $checkoutResponse = $this->commandBus->dispatch(new CreateCheckoutSessionCommand(
                workspaceId: $command->workspaceId,
                plan: $command->newPlan,
                billingPeriod: $command->billingPeriod,
                couponCode: null,
                successUrl: route('settings.billing') . '?plan_changed=success',
                cancelUrl: route('billing.change-plan'),
            ));

            return new ChangePlanResponse(
                action: 'checkout',
                checkoutUrl: $checkoutResponse->checkoutUrl,
            );
        }

        // 5. Handle downgrade to free - cancel subscription
        if ($newPlan === Plan::FREE) {
            // Call domain method - it will emit SubscriptionCancellationRequested event
            $workspace->cancelSubscription();

            // Save workspace and dispatch events
            $this->workspaceRepository->save($workspace);
            $this->eventBus->dispatch(...$workspace->pullEvents());

            return new ChangePlanResponse(
                action: 'cancelled',
                message: 'Your subscription will be cancelled at the end of your billing period. You will be downgraded to the Free plan.',
            );
        }

        // 6. Handle downgrade between paid plans
        // Call domain method - it will emit PlanDowngradeScheduled event
        $workspace->schedulePlanDowngrade($newPlan, $command->billingPeriod);

        // Save workspace and dispatch events
        $this->workspaceRepository->save($workspace);
        $this->eventBus->dispatch(...$workspace->pullEvents());

        return new ChangePlanResponse(
            action: 'scheduled',
            message: "Your plan will be changed to {$newPlan->displayName()} at the end of your current billing period.",
        );
    }

    private function isUpgrade(Plan $current, Plan $new): bool
    {
        $planOrder = [
            Plan::FREE->value => 0,
            Plan::STARTER->value => 1,
            Plan::PRO->value => 2,
            Plan::ENTERPRISE->value => 3,
        ];

        return ($planOrder[$new->value] ?? 0) > ($planOrder[$current->value] ?? 0);
    }
}
