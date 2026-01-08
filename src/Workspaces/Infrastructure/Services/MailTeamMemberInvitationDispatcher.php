<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Workspaces\Domain\Services\TeamMemberInvitationDispatcher;
use Modules\Workspaces\Domain\ValueObjects\Email;
use Modules\Workspaces\Infrastructure\Mail\WorkspaceInvitationMail;

final readonly class MailTeamMemberInvitationDispatcher implements TeamMemberInvitationDispatcher
{
    public function dispatch(
        Email $email,
        string $token,
        string $workspaceId,
        string $role,
    ): void {
        // Log the invitation
        Log::info('Sending workspace invitation email', [
            'workspace_id' => $workspaceId,
            'email' => $email->value,
            'role' => $role,
        ]);

        // Send invitation email
        Mail::to($email->value)->send(new WorkspaceInvitationMail(
            token: $token,
            workspaceId: $workspaceId,
            role: $role,
        ));
    }
}
