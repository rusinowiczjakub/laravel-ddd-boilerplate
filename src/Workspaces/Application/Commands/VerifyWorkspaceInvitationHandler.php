<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Workspaces\Domain\Exceptions\InvitationExpiredException;
use Modules\Workspaces\Domain\Exceptions\InvitationNotFoundException;
use Modules\Workspaces\Domain\Exceptions\InvitationRequiresLoginException;
use Modules\Workspaces\Domain\Exceptions\InvitationRequiresRegistrationException;
use Modules\Workspaces\Domain\Repositories\WorkspaceInvitationRepository;
use Modules\Workspaces\Domain\Services\MemberInvitationSessionManager;
use Modules\Workspaces\Domain\Services\UserExistenceChecker;
use Modules\Workspaces\Domain\ValueObjects\InvitationToken;

#[CommandHandler(VerifyWorkspaceInvitationCommand::class)]
final readonly class VerifyWorkspaceInvitationHandler
{
    public function __construct(
        private WorkspaceInvitationRepository $invitationRepository,
        private UserExistenceChecker $userExistenceChecker,
        private MemberInvitationSessionManager $sessionManager,
    ) {
    }

    /**
     * @throws InvitationNotFoundException
     * @throws InvitationExpiredException
     * @throws InvitationRequiresLoginException
     * @throws InvitationRequiresRegistrationException
     */
    public function handle(VerifyWorkspaceInvitationCommand $command): void
    {
        // Find invitation
        $invitation = $this->invitationRepository->findByToken(
            InvitationToken::fromString($command->token)
        );

        if (!$invitation) {
            throw new InvitationNotFoundException('Invitation not found');
        }

        // Check if expired
        if ($invitation->isExpired()) {
            throw new InvitationExpiredException('Invitation has expired');
        }

        // Store pending invitation in session
        $this->sessionManager->storePendingInvitation(
            $command->token,
            $invitation->email()->value
        );

        // Check if user exists with this email
        $userExists = $this->userExistenceChecker->existsByEmail(
            $invitation->email()->value
        );

        if ($userExists) {
            // User has account - redirect to login
            throw new InvitationRequiresLoginException($invitation->email()->value);
        }

        // User doesn't have account - redirect to register
        throw new InvitationRequiresRegistrationException($invitation->email()->value);
    }
}
