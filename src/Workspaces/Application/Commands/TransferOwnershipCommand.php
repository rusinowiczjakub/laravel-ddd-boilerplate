<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Command\Contracts\Command;

final readonly class TransferOwnershipCommand implements Command
{
    public function __construct(
        public string $workspaceId,
        public string $newOwnerId,
        public string $currentOwnerId,
    ) {
    }
}
