<?php

declare(strict_types=1);

namespace Modules\IAM\Application\Responses;

use Modules\IAM\Domain\ValueObjects\Email;
use Modules\Shared\Domain\ValueObjects\Id;

final readonly class UserLoggedInResponse
{
    public function __construct(
        public Id $userId,
        public Email $email,
        public string $name,
        public bool $remember,
    ) {
    }
}
