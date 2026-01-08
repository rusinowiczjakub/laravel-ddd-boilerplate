<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Command\Contracts\Command;

final readonly class InviteMemberCommand implements Command
{
    public function __construct(
        public string $workspaceId,
        public string $email,
        public string $role,
        public string $invitedBy, // User ID who is inviting
    ) {
    }
}
