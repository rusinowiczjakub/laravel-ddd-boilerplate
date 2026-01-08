<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Illuminate\Session\SessionManager;
use Modules\Core\Attributes\CommandHandler;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Exceptions\UnauthorizedWorkspaceAccessException;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[CommandHandler(SwitchWorkspaceCommand::class)]
final readonly class SwitchWorkspaceHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private SessionManager $session,
    ) {
    }

    /**
     * @throws WorkspaceNotFound
     * @throws UnauthorizedWorkspaceAccessException
     */
    public function handle(SwitchWorkspaceCommand $command): void
    {
        $workspace = $this->workspaceRepository->findById(
            WorkspaceId::fromString($command->workspaceId)
        );

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        // Check if user has access to this workspace (owner check)
        // TODO: Later add team member check
        if ($workspace->ownerId()->value() !== $command->userId) {
            throw new UnauthorizedWorkspaceAccessException('User does not have access to this workspace');
        }

        // Store workspace ID in session
        $this->session->put('current_workspace_id', $workspace->id()->value());
    }
}
