<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;

/**
 * ActivatePlanHandler - Activates a workspace plan after Stripe webhook confirmation.
 *
 * This handler is called when a subscription is confirmed by Stripe webhooks,
 * NOT during checkout (event-driven approach).
 */
#[CommandHandler(ActivatePlanCommand::class)]
final readonly class ActivatePlanHandler
{
    public function handle(ActivatePlanCommand $command): void
    {
        // Load workspace (Infrastructure access OK in handler for this simple case)
        $workspace = WorkspaceModel::find($command->workspaceId->value());

        if (! $workspace) {
            \Log::warning('Workspace not found for plan activation', [
                'workspace_id' => $command->workspaceId->value(),
                'plan' => $command->plan,
            ]);

            return;
        }

        // Update plan
        $workspace->plan = $command->plan;
        $workspace->save();

        \Log::info('Plan activated for workspace', [
            'workspace_id' => $workspace->id,
            'plan' => $command->plan,
            'subscription_id' => $command->subscriptionId,
        ]);
    }
}
