<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Exceptions\InvitationNotFoundException;
use Modules\Workspaces\Domain\Exceptions\UnauthorizedWorkspaceAccessException;
use Modules\Workspaces\Domain\Repositories\WorkspaceInvitationRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[CommandHandler(CancelInvitationCommand::class)]
final readonly class CancelInvitationHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private WorkspaceInvitationRepository $invitationRepository,
        private WorkspaceMemberRepository $memberRepository,
    ) {
    }

    /**
     * @throws WorkspaceNotFound
     * @throws InvitationNotFoundException
     * @throws UnauthorizedWorkspaceAccessException
     */
    public function handle(CancelInvitationCommand $command): void
    {
        $workspaceId = WorkspaceId::fromString($command->workspaceId);
        $workspace = $this->workspaceRepository->findById($workspaceId);

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        // Find the invitation
        $invitationId = Id::fromString($command->invitationId);
        $invitation = $this->invitationRepository->findById($invitationId);

        if (!$invitation || $invitation->workspaceId()->value() !== $workspaceId->value()) {
            throw new InvitationNotFoundException('Invitation not found');
        }

        // Check if canceller has admin rights
        $cancellerId = Id::fromString($command->cancelledById);
        $isOwner = $workspace->ownerId()->value() === $cancellerId->value();
        $cancellerMember = $this->memberRepository->findByWorkspaceAndUser($workspaceId, $cancellerId);

        if (!$isOwner && (!$cancellerMember || !$cancellerMember->isAdministrator())) {
            throw new UnauthorizedWorkspaceAccessException('Only administrators can cancel invitations');
        }

        // Cancel the invitation
        $invitation->cancel();
        $this->invitationRepository->save($invitation);
    }
}
