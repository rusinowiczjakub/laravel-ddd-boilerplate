<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Modules\Core\Query\Contracts\Query;

final readonly class GetUserWorkspacesQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {
    }
}
