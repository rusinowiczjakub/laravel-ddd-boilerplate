<?php

declare(strict_types=1);

namespace Modules\Billing\Domain\Repositories;

use Modules\Billing\Domain\Models\BillingWorkspace;
use Modules\Shared\Domain\ValueObjects\Uuid;

interface BillingWorkspaceRepository
{
    public function findById(Uuid $workspaceId): ?BillingWorkspace;

    public function save(BillingWorkspace $workspace): void;
}
