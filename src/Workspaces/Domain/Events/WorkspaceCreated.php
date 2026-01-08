<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Events;

use Modules\Core\Events\Contracts\Event;
use Modules\Core\Events\DomainEvent;
use Modules\Workspaces\Domain\Models\Workspace;

final class WorkspaceCreated extends DomainEvent
{
    public function __construct(
        string $workspaceId,
        public readonly string $name,
        public readonly string $slug,
        public readonly string $plan,
        public readonly string $ownerId,
    ) {
        parent::__construct($workspaceId, Workspace::class);
    }

    public function toPayload(): array
    {
        return [
            'workspace_id' => $this->aggregateId(),
            'name' => $this->name,
            'slug' => $this->slug,
            'plan' => $this->plan,
            'owner_id' => $this->ownerId,
        ];
    }

    public static function fromPayload(array $payload): Event
    {
        return new self(
            $payload['workspace_id'],
            $payload['name'],
            $payload['slug'],
            $payload['plan'],
            $payload['owner_id'],
        );
    }
}
