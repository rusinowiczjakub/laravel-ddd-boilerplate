<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Collections;

use Modules\Core\Collections\Collection;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Exceptions\MemberNotFoundException;
use Modules\Workspaces\Domain\Models\WorkspaceMember;

final class WorkspaceMemberCollection extends Collection
{
    public function findByUserId(Id $userId): ?WorkspaceMember
    {
        return $this->first(fn(WorkspaceMember $member) => $member->userId()->value() === $userId->value());
    }

    public function findById(Id $memberId): ?WorkspaceMember
    {
        return $this->first(fn(WorkspaceMember $member) => $member->id()->value() === $memberId->value());
    }

    public function userExists(Id $userId): bool
    {
        return $this->contains(fn(WorkspaceMember $member) => $member->userId()->value() === $userId->value());
    }

    public function removeByUserId(Id $userId): void
    {
        $index = $this->search(fn(WorkspaceMember $member) => $member->userId()->value() === $userId->value());

        if ($index === false) {
            throw new MemberNotFoundException('Member not found in collection');
        }

        $this->forget($index);
    }

    public function administrators(): self
    {
        return $this->filter(fn(WorkspaceMember $member) => $member->isAdministrator());
    }

    public function collaborators(): self
    {
        return $this->filter(fn(WorkspaceMember $member) => $member->isCollaborator());
    }

    public function getUserRole(Id $userId): ?WorkspaceRole
    {
        $member = $this->findByUserId($userId);
        return $member?->role();
    }
}
