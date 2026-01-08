<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Services;

use Modules\Workspaces\Domain\ValueObjects\Email;

interface TeamMemberInvitationDispatcher
{
    /**
     * Send invitation email to team member.
     */
    public function dispatch(
        Email $email,
        string $token,
        string $workspaceId,
        string $role,
    ): void;
}
