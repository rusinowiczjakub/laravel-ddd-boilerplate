<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Modules\Core\Query\Contracts\Query;

final readonly class GetWorkspaceUsageQuery implements Query
{
    public function __construct(
        public string $workspaceId,
    ) {
    }
}
