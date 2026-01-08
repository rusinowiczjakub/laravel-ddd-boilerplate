<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Workspaces\Domain\Models\Workspace;

final class MemberJoined extends DomainEvent
{
    public function __construct(
        string $workspaceId,
        public readonly string $memberId,
        public readonly string $userId,
        public readonly string $role,
    ) {
        parent::__construct($workspaceId, Workspace::class);
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->aggregateId(),
            'member_id' => $this->memberId,
            'user_id' => $this->userId,
            'role' => $this->role,
        ];
    }

    public static function fromPayload(array $payload): \Modules\Core\Events\Contracts\Event
    {
        return new self(
            $payload['workspace_id'],
            $payload['member_id'],
            $payload['user_id'],
            $payload['role'],
        );
    }
}
