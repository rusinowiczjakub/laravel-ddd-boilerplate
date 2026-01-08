<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

use Modules\Workspaces\Domain\Models\Plan;
use Modules\Workspaces\Domain\ValueObjects\Name;
use Modules\Workspaces\Domain\ValueObjects\Slug;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

final readonly class WorkspaceCreatedResponse
{
    public function __construct(
        public WorkspaceId $workspaceId,
        public Name $name,
        public Slug $slug,
        public Plan $plan,
    ) {
    }
}
