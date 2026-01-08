<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\WorkspaceCache;
use Modules\Core\Attributes\Subscribe;
use Modules\Workspaces\Domain\Events\WorkspaceCreated;

final readonly class InvalidateWorkspaceCacheOnWorkspaceCreated
{
    public function __construct(
        private WorkspaceCache $cache,
    ) {}

    #[Subscribe(WorkspaceCreated::class)]
    public function handle(WorkspaceCreated $event): void
    {
        // Invalidate workspace list for the owner
        $this->cache->invalidateUserWorkspaces($event->ownerId);
    }
}
