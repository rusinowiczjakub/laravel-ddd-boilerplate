<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Repositories;

use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Models\WorkspaceMember;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

interface WorkspaceMemberRepository
{
    public function save(WorkspaceMember $member): void;

    public function delete(WorkspaceMember $member): void;

    public function findById(Id $id): ?WorkspaceMember;

    public function findByWorkspaceAndUser(WorkspaceId $workspaceId, Id $userId): ?WorkspaceMember;

    /**
     * @return WorkspaceMember[]
     */
    public function findByWorkspace(WorkspaceId $workspaceId): array;

    /**
     * @return WorkspaceMember[]
     */
    public function findByUser(Id $userId): array;
}
