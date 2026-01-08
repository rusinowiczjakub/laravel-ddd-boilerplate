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

#[CommandHandler(TransferOwnershipCommand::class)]
final readonly class TransferOwnershipHandler
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
    public function handle(TransferOwnershipCommand $command): void
    {
        $workspaceId = WorkspaceId::fromString($command->workspaceId);
        $workspace = $this->workspaceRepository->findById($workspaceId);

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        // Only current owner can transfer ownership
        $currentOwnerId = Id::fromString($command->currentOwnerId);
        if ($workspace->ownerId()->value() !== $currentOwnerId->value()) {
            throw new UnauthorizedWorkspaceAccessException('Only the workspace owner can transfer ownership');
        }

        // Check new owner is a member of the workspace
        $newOwnerId = Id::fromString($command->newOwnerId);
        $newOwnerMember = $this->memberRepository->findByWorkspaceAndUser($workspaceId, $newOwnerId);

        if (!$newOwnerMember) {
            throw new MemberNotFoundException('New owner must be a member of the workspace');
        }

        // Transfer ownership
        $workspace->transferOwnership($newOwnerId);
        $this->workspaceRepository->save($workspace);

        // Remove current owner from the workspace (they're leaving)
        $currentOwnerMember = $this->memberRepository->findByWorkspaceAndUser($workspaceId, $currentOwnerId);
        if ($currentOwnerMember) {
            $this->memberRepository->delete($currentOwnerMember);
        }
    }
}
