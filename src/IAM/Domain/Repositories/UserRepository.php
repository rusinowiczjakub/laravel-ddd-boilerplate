<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Repositories;

use Modules\IAM\Domain\Models\User;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\Shared\Domain\ValueObjects\Id;

interface UserRepository
{
    public function save(User $user): void;

    public function findById(Id $id): ?User;

    public function findByEmail(Email $email): ?User;

    public function emailExists(Email $email): bool;
}
