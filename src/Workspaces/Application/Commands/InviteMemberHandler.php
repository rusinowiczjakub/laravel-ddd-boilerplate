<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Application\Responses\InvitationCreatedResponse;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Events\MemberInvited;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Exceptions\UnauthorizedWorkspaceAccessException;
use Modules\Workspaces\Domain\Models\WorkspaceInvitation;
use Modules\Workspaces\Domain\Repositories\WorkspaceInvitationRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\Email;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[CommandHandler(InviteMemberCommand::class)]
final readonly class InviteMemberHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private WorkspaceInvitationRepository $invitationRepository,
        private WorkspaceMemberRepository $memberRepository,
        private EventBus $eventBus,
    ) {
    }

    /**
     * @throws WorkspaceNotFound
     * @throws UnauthorizedWorkspaceAccessException
     */
    public function handle(InviteMemberCommand $command): InvitationCreatedResponse
    {
        $workspaceId = WorkspaceId::fromString($command->workspaceId);
        $workspace = $this->workspaceRepository->findById($workspaceId);

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        // Check if inviter has admin rights
        $inviterId = Id::fromString($command->invitedBy);
        $isOwner = $workspace->ownerId()->value() === $inviterId->value();
        $member = $this->memberRepository->findByWorkspaceAndUser($workspaceId, $inviterId);

        if (!$isOwner && (!$member || !$member->isAdministrator())) {
            throw new UnauthorizedWorkspaceAccessException('Only administrators can invite members');
        }

        // Invite member through aggregate
        $invitation = $workspace->inviteMember(
            email: Email::fromString($command->email),
            role: WorkspaceRole::from($command->role),
            invitedBy: $inviterId,
        );

        $this->invitationRepository->save($invitation);
        $this->eventBus->dispatch(...$workspace->pullEvents());

        return new InvitationCreatedResponse(
            invitationId: $invitation->id(),
            email: $invitation->email(),
            token: $invitation->token(),
            expiresAt: $invitation->expiresAt(),
        );
    }
}
