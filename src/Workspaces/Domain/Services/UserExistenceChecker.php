<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Services;

interface UserExistenceChecker
{
    /**
     * Check if a user exists with the given email.
     */
    public function existsByEmail(string $email): bool;
}
