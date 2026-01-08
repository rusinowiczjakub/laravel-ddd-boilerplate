<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Modules\Core\Attributes\QueryHandler;
use Modules\Workspaces\Application\Responses\WorkspaceResponse;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[QueryHandler(GetWorkspaceByIdQuery::class)]
final readonly class GetWorkspaceByIdHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
    ) {
    }

    /**
     * @throws WorkspaceNotFound
     */
    public function handle(GetWorkspaceByIdQuery $query): WorkspaceResponse
    {
        $workspace = $this->workspaceRepository->findById(
            WorkspaceId::fromString($query->workspaceId)
        );

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        return WorkspaceResponse::fromWorkspace($workspace);
    }
}
