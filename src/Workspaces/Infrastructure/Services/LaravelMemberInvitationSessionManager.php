<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Services;

use Modules\Workspaces\Domain\Services\MemberInvitationSessionManager;

final readonly class LaravelMemberInvitationSessionManager implements MemberInvitationSessionManager
{
    private const TOKEN_KEY = 'pending_invitation_token';
    private const EMAIL_KEY = 'pending_invitation_email';

    public function storePendingInvitation(string $token, string $email): void
    {
        session([
            self::TOKEN_KEY => $token,
            self::EMAIL_KEY => $email,
        ]);
    }

    public function getPendingInvitationToken(): ?string
    {
        return session(self::TOKEN_KEY);
    }

    public function getPendingInvitationEmail(): ?string
    {
        return session(self::EMAIL_KEY);
    }

    public function hasPendingInvitation(): bool
    {
        return session()->has(self::TOKEN_KEY);
    }

    public function clearPendingInvitation(): void
    {
        session()->forget([
            self::TOKEN_KEY,
            self::EMAIL_KEY,
        ]);
    }
}
