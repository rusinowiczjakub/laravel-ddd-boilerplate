<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Workspaces\Domain\Enums\WorkspaceStatus;
use Modules\Workspaces\Domain\Models\Plan;
use Modules\Workspaces\Domain\Models\Workspace;
use Modules\Workspaces\Domain\ValueObjects\Name;
use Modules\Workspaces\Domain\ValueObjects\Slug;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

final readonly class WorkspaceResponse
{
    public function __construct(
        public WorkspaceId $id,
        public Name $name,
        public Slug $slug,
        public Plan $plan,
        public WorkspaceStatus $status,
        public Date $createdAt,
    ) {
    }

    public static function fromWorkspace(Workspace $workspace): self
    {
        return new self(
            id: $workspace->id(),
            name: $workspace->name(),
            slug: $workspace->slug(),
            plan: $workspace->plan(),
            status: $workspace->status(),
            createdAt: $workspace->createdAt(),
        );
    }
}
