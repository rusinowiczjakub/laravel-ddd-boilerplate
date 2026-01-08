<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Repositories;

use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Models\Workspace;
use Modules\Workspaces\Domain\ValueObjects\Slug;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

interface WorkspaceRepository
{
    public function save(Workspace $workspace): void;

    public function findById(WorkspaceId $id): ?Workspace;

    public function findBySlug(Slug $slug): ?Workspace;

    public function findByApiKeyHash(string $hash): ?Workspace;

    /**
     * @return Workspace[]
     */
    public function findByOwnerId(Id $ownerId): array;
}
