<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\Name;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[CommandHandler(UpdateWorkspaceCommand::class)]
final readonly class UpdateWorkspaceHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
    ) {
    }

    /**
     * @throws WorkspaceNotFound
     */
    public function handle(UpdateWorkspaceCommand $command): void
    {
        $workspace = $this->workspaceRepository->findById(
            WorkspaceId::fromString($command->workspaceId)
        );

        if ($workspace === null) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        if ($command->name !== null) {
            $workspace->rename(Name::fromString($command->name));
        }

        $this->workspaceRepository->save($workspace);
    }
}
