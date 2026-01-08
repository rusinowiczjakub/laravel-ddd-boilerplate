<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

final readonly class WorkspaceListResponse
{
    /**
     * @param WorkspaceResponse[] $workspaces
     */
    public function __construct(
        public array $workspaces,
    ) {
    }
}
