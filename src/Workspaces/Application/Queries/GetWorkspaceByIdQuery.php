<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

final readonly class GetWorkspaceByIdQuery
{
    public function __construct(
        public string $workspaceId,
    ) {
    }
}
