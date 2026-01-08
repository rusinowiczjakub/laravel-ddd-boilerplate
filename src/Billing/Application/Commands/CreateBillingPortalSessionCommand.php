<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Core\Command\Contracts\Command;
use Modules\Shared\Domain\ValueObjects\Uuid;

final readonly class CreateBillingPortalSessionCommand implements Command
{
    public function __construct(
        public Uuid $workspaceId,
        public string $returnUrl,
    ) {}
}
