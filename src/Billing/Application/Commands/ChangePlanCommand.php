<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Core\Command\Contracts\Command;
use Modules\Shared\Domain\ValueObjects\Id;

final readonly class ChangePlanCommand implements Command
{
    public function __construct(
        public Id $workspaceId,
        public string $newPlan, // 'free', 'starter', 'pro'
        public string $billingPeriod = 'monthly', // 'monthly' or 'yearly'
    ) {}
}
