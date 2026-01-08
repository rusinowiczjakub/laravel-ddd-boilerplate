<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Exceptions\MemberNotFoundException;
use Modules\Workspaces\Domain\Exceptions\UnauthorizedWorkspaceAccessException;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[CommandHandler(RemoveMemberCommand::class)]
final readonly class RemoveMemberHandler
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
    public function handle(RemoveMemberCommand $command): void
    {
        $workspaceId = WorkspaceId::fromString($command->workspaceId);
        $workspace = $this->workspaceRepository->findById($workspaceId);

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        // Find member to remove
        $memberId = Id::fromString($command->memberId);
        $memberToRemove = $this->memberRepository->findById($memberId);

        if (!$memberToRemove || $memberToRemove->workspaceId()->value() !== $workspaceId->value()) {
            throw new MemberNotFoundException('Member not found');
        }

        // Check if remover has admin rights
        $removerId = Id::fromString($command->removedById);
        $isOwner = $workspace->ownerId()->value() === $removerId->value();
        $removerMember = $this->memberRepository->findByWorkspaceAndUser($workspaceId, $removerId);

        // Owner can't be removed (must transfer ownership first)
        if ($memberToRemove->userId()->value() === $workspace->ownerId()->value()) {
            throw new UnauthorizedWorkspaceAccessException('Cannot remove workspace owner. Transfer ownership first.');
        }

        // Check authorization (owner, admin, or self-removal)
        $isSelfRemoval = $memberToRemove->userId()->value() === $removerId->value();
        $canRemove = $isOwner || ($removerMember && $removerMember->isAdministrator()) || $isSelfRemoval;

        if (!$canRemove) {
            throw new UnauthorizedWorkspaceAccessException('You do not have permission to remove this member');
        }

        $this->memberRepository->delete($memberToRemove);
    }
}
