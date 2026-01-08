<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Events;

use Modules\Core\Events\DomainEvent;
use Modules\Workspaces\Domain\Models\Workspace;

final class MemberInvited extends DomainEvent
{
    public function __construct(
        string $workspaceId,
        public readonly string $invitationId,
        public readonly string $email,
        public readonly string $role,
        public readonly string $invitedBy,
        public readonly string $token,
    ) {
        parent::__construct($workspaceId, Workspace::class);
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->aggregateId(),
            'invitation_id' => $this->invitationId,
            'email' => $this->email,
            'role' => $this->role,
            'invited_by' => $this->invitedBy,
            'token' => $this->token,
        ];
    }

    public static function fromPayload(array $payload): \Modules\Core\Events\Contracts\Event
    {
        return new self(
            $payload['workspace_id'],
            $payload['invitation_id'],
            $payload['email'],
            $payload['role'],
            $payload['invited_by'],
            $payload['token'],
        );
    }
}
