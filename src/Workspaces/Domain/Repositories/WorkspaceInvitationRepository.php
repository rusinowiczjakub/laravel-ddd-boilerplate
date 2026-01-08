<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Repositories;

use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Models\WorkspaceInvitation;
use Modules\Workspaces\Domain\ValueObjects\InvitationToken;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

interface WorkspaceInvitationRepository
{
    public function save(WorkspaceInvitation $invitation): void;

    public function findById(Id $id): ?WorkspaceInvitation;

    public function findByToken(InvitationToken $token): ?WorkspaceInvitation;

    /**
     * @return WorkspaceInvitation[]
     */
    public function findPendingByWorkspace(WorkspaceId $workspaceId): array;
}
