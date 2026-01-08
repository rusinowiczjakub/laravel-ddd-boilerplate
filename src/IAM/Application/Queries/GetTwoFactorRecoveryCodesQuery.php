<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Queries;

use Modules\Core\Query\Contracts\Query;

final readonly class GetTwoFactorRecoveryCodesQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {
    }
}
