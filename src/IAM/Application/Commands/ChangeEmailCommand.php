<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Command\Contracts\Command;

final readonly class ChangeEmailCommand implements Command
{
    public function __construct(
        public string $userId,
        public string $password,
        public string $newEmail,
    ) {
    }
}
