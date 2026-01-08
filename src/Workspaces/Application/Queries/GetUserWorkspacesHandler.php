<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Modules\Core\Attributes\QueryHandler;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Application\Responses\WorkspaceListResponse;
use Modules\Workspaces\Application\Responses\WorkspaceResponse;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;

#[QueryHandler(GetUserWorkspacesQuery::class)]
final readonly class GetUserWorkspacesHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
    ) {
    }

    public function handle(GetUserWorkspacesQuery $query): WorkspaceListResponse
    {
        $workspaces = $this->workspaceRepository->findByOwnerId(
            Id::fromString($query->userId)
        );

        return new WorkspaceListResponse(
            workspaces: array_map(
                fn($workspace) => WorkspaceResponse::fromWorkspace($workspace),
                $workspaces
            )
        );
    }
}
