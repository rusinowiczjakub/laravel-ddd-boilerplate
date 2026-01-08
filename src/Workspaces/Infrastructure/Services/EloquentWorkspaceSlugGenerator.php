<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Services;

use Illuminate\Support\Str;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Services\WorkspaceSlugGenerator;
use Modules\Workspaces\Domain\ValueObjects\Slug;
use Modules\Workspaces\Infrastructure\Models\WorkspaceModel;

class EloquentWorkspaceSlugGenerator implements WorkspaceSlugGenerator
{
    public function generate(string $name, Id $ownerId): Slug
    {
        $slug = Str::slug($name);

        // Check uniqueness within owner's workspaces only
        $workspacesCount = WorkspaceModel::where('owner_id', $ownerId->value())
            ->where('slug', $slug)
            ->count();

        return Slug::fromString(
            $workspacesCount > 0
                ? "{$slug}-{$workspacesCount}"
                : $slug
        );
    }
}
