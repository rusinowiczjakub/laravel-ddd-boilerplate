<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Models;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

final class WorkspaceMember
{
    private function __construct(
        private Id $id,
        private WorkspaceId $workspaceId,
        private Id $userId,
        private WorkspaceRole $role,
        private Date $addedAt,
    ) {
    }

    public static function create(
        WorkspaceId $workspaceId,
        Id $userId,
        WorkspaceRole $role,
    ): self {
        return new self(
            id: Id::create(),
            workspaceId: $workspaceId,
            userId: $userId,
            role: $role,
            addedAt: new Date(),
        );
    }

    /**
     * @param array{
     *     id: Id,
     *     workspaceId: WorkspaceId,
     *     userId: Id,
     *     role: WorkspaceRole,
     *     addedAt: Date
     * } $data
     */
    public static function reconstitute(array $data): self
    {
        return new self(
            id: $data['id'],
            workspaceId: $data['workspaceId'],
            userId: $data['userId'],
            role: $data['role'],
            addedAt: $data['addedAt'],
        );
    }

    public function changeRole(WorkspaceRole $newRole): void
    {
        $this->role = $newRole;
    }

    public function can(string $permission): bool
    {
        return $this->role->can($permission);
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function workspaceId(): WorkspaceId
    {
        return $this->workspaceId;
    }

    public function userId(): Id
    {
        return $this->userId;
    }

    public function role(): WorkspaceRole
    {
        return $this->role;
    }

    public function addedAt(): Date
    {
        return $this->addedAt;
    }

    public function isAdministrator(): bool
    {
        return $this->role->isAdministrator();
    }

    public function isCollaborator(): bool
    {
        return $this->role->isCollaborator();
    }
}
