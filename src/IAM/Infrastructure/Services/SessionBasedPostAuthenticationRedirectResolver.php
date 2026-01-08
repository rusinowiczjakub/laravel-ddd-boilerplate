<?php

declare(strict_types=1);

namespace Modules\IAM\Infrastructure\Services;

use Modules\IAM\Domain\Services\PostAuthenticationRedirectResolver;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Services\MemberInvitationSessionManager;

final readonly class SessionBasedPostAuthenticationRedirectResolver implements PostAuthenticationRedirectResolver
{
    public function __construct(
        private MemberInvitationSessionManager $sessionManager,
        private WorkspaceMemberRepository $memberRepository,
    ) {
    }

    public function resolve(Id $userId): string
    {
        // Priority 1: Check for pending invitation
        $pendingToken = $this->sessionManager->getPendingInvitationToken();
        if ($pendingToken) {
            return route('invitations.accept', ['token' => $pendingToken]);
        }

        // Priority 2: Check if user has workspaces
        $userWorkspaces = $this->memberRepository->findByUser($userId);
        if (count($userWorkspaces) > 0) {
            return route('dashboard');
        }

        // Priority 3: No workspaces - go to onboarding
        return route('onboarding.create-workspace');
    }
}
