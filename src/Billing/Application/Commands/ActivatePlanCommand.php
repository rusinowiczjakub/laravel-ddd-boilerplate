<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Core\Command\Contracts\Command;
use Modules\Shared\Domain\ValueObjects\Uuid;

/**
 * ActivatePlanCommand - Activates a plan for a workspace after Stripe confirms subscription.
 */
final readonly class ActivatePlanCommand implements Command
{
    public function __construct(
        public Uuid $workspaceId,
        public string $plan,
        public string $subscriptionId,
    ) {}
}
