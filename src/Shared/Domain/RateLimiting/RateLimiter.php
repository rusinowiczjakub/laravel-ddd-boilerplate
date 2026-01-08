<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\RateLimiting;

use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

interface RateLimiter
{
    public function attempt(WorkspaceId $workspaceId, RateLimitAction $action): RateLimitStatus;

    public function status(WorkspaceId $workspaceId, RateLimitAction $action): RateLimitStatus;

    public function reset(WorkspaceId $workspaceId, RateLimitAction $action): void;
}
