<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Commands;

use Modules\Core\Command\Contracts\Command;

final readonly class RegisterUserCommand implements Command
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    ) {
    }
}
