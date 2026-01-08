<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Exceptions\MemberNotFoundException;
use Modules\Workspaces\Domain\Exceptions\UnauthorizedWorkspaceAccessException;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[CommandHandler(ChangeMemberRoleCommand::class)]
final readonly class ChangeMemberRoleHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private WorkspaceMemberRepository $memberRepository,
    ) {
    }

    /**
     * @throws WorkspaceNotFound
     * @throws MemberNotFoundException
     * @throws UnauthorizedWorkspaceAccessException
     */
    public function handle(ChangeMemberRoleCommand $command): void
    {
        $workspaceId = WorkspaceId::fromString($command->workspaceId);
        $workspace = $this->workspaceRepository->findById($workspaceId);

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        // Find member to update
        $memberId = Id::fromString($command->memberId);
        $memberToUpdate = $this->memberRepository->findById($memberId);

        if (!$memberToUpdate || $memberToUpdate->workspaceId()->value() !== $workspaceId->value()) {
            throw new MemberNotFoundException('Member not found');
        }

        // Check if changer has admin rights
        $changerId = Id::fromString($command->changedById);
        $isOwner = $workspace->ownerId()->value() === $changerId->value();
        $changerMember = $this->memberRepository->findByWorkspaceAndUser($workspaceId, $changerId);

        if (!$isOwner && (!$changerMember || !$changerMember->isAdministrator())) {
            throw new UnauthorizedWorkspaceAccessException('Only administrators can change member roles');
        }

        // Owner's role can't be changed
        if ($memberToUpdate->userId()->value() === $workspace->ownerId()->value()) {
            throw new UnauthorizedWorkspaceAccessException('Cannot change workspace owner role');
        }

        // Admin cannot change their own role (only owner can)
        if (!$isOwner && $memberToUpdate->userId()->value() === $changerId->value()) {
            throw new UnauthorizedWorkspaceAccessException('You cannot change your own role');
        }

        $newRole = WorkspaceRole::from($command->role);
        $memberToUpdate->changeRole($newRole);

        $this->memberRepository->save($memberToUpdate);
    }
}
