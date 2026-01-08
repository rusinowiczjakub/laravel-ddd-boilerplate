<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

use Modules\Workspaces\Domain\Enums\WorkspaceRole;

final readonly class MemberResponse
{
    public function __construct(
        public string $id,
        public ?string $userId,
        public ?string $name,
        public string $email,
        public WorkspaceRole $role,
        public string $addedAt,
        public bool $isOwner = false,
        public string $status = 'active', // 'active' or 'pending'
        public ?string $invitationId = null,
    ) {
    }

    /**
     * @return array{id: string, userId: string|null, name: string|null, email: string, role: string, addedAt: string, isOwner: bool, status: string, invitationId: string|null}
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'name' => $this->name,
            'email' => $this->email,
            'role' => $this->role->value,
            'addedAt' => $this->addedAt,
            'isOwner' => $this->isOwner,
            'status' => $this->status,
            'invitationId' => $this->invitationId,
        ];
    }
}
