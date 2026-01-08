<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Command\Contracts\Command;

final readonly class CreateWorkspaceCommand implements Command
{
    public function __construct(
        public string $name,
        public string $plan,
        public string $ownerId,
    ) {
    }
}
