<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Repositories;

use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Domain\ValueObjects\EmailVerificationCode;

interface EmailVerificationSessionRepository
{
    public function createSession(Email $email): EmailVerificationCode;

    public function findByEmail(Email $email): ?array;

    public function verify(Email $email, EmailVerificationCode $code): bool;

    public function incrementAttempts(Email $email): void;

    public function deleteSession(Email $email): void;
}
