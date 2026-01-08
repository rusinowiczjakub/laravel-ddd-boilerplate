<?php

namespace Modules\Workspaces\Domain\Services;

use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\ValueObjects\Slug;

interface WorkspaceSlugGenerator
{
    public function generate(string $name, Id $ownerId): Slug;
}
