<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Services;

use Illuminate\Support\Facades\DB;
use Modules\Workspaces\Domain\Services\UserExistenceChecker;

final readonly class EloquentUserExistenceChecker implements UserExistenceChecker
{
    public function existsByEmail(string $email): bool
    {
        return DB::table('users')
            ->where('email', $email)
            ->exists();
    }
}
