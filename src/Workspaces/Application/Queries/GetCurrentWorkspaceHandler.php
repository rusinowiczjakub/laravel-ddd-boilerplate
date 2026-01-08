<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Illuminate\Support\Facades\Session;
use Modules\Core\Attributes\QueryHandler;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Models\Workspace;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[QueryHandler(GetCurrentWorkspaceQuery::class)]
final readonly class GetCurrentWorkspaceHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private WorkspaceMemberRepository $memberRepository,
    ) {
    }

    public function __invoke(GetCurrentWorkspaceQuery $query): ?Workspace
    {
        $userId = Id::fromString($query->userId);

        // Check if there's a current workspace in session
        $currentWorkspaceId = Session::get('current_workspace_id');

        if ($currentWorkspaceId) {
            $workspaceId = WorkspaceId::fromString($currentWorkspaceId);
            $workspace = $this->workspaceRepository->findById($workspaceId);

            // Verify user has access to this workspace
            if ($workspace) {
                $member = $this->memberRepository->findByWorkspaceAndUser($workspaceId, $userId);
                if ($member) {
                    return $workspace;
                }
            }
        }

        // If no current workspace or user doesn't have access, get the first workspace
        $members = $this->memberRepository->findByUser($userId);

        if (empty($members)) {
            return null;
        }

        $firstMember = $members[0];
        $workspace = $this->workspaceRepository->findById($firstMember->workspaceId());

        // Set it as current workspace
        if ($workspace) {
            Session::put('current_workspace_id', $workspace->id()->value());
        }

        return $workspace;
    }
}
