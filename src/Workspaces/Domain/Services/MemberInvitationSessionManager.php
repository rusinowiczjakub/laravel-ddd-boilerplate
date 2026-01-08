<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Services;

interface MemberInvitationSessionManager
{
    /**
     * Store pending invitation details in session.
     */
    public function storePendingInvitation(string $token, string $email): void;

    /**
     * Get pending invitation token if exists.
     */
    public function getPendingInvitationToken(): ?string;

    /**
     * Get pending invitation email if exists.
     */
    public function getPendingInvitationEmail(): ?string;

    /**
     * Check if there's a pending invitation.
     */
    public function hasPendingInvitation(): bool;

    /**
     * Clear pending invitation from session.
     */
    public function clearPendingInvitation(): void;
}
