<?php

declare(strict_types=1);

namespace Modules\IAM\Infrastructure\Repositories;

use Modules\Core\Models\User as UserModel;
use Modules\IAM\Domain\Models\User;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Infrastructure\Hydrators\UserHydrator;
use Modules\Shared\Domain\ValueObjects\Id;

final readonly class EloquentUserRepository implements UserRepository
{
    public function __construct(
        private UserHydrator $hydrator,
    ) {
    }

    public function save(User $user): void
    {
        $model = UserModel::query()->find($user->id()->value());

        if (!$model) {
            $model = $this->hydrator->toModel($user);
        } else {
            $updatedModel = $this->hydrator->toModel($user);
            $model->name = $updatedModel->name;
            $model->email = $updatedModel->email;
            $model->password = $updatedModel->password;
            $model->first_name = $updatedModel->first_name;
            $model->last_name = $updatedModel->last_name;
            $model->phone = $updatedModel->phone;
            $model->email_verified_at = $updatedModel->email_verified_at;
            $model->phone_verified_at = $updatedModel->phone_verified_at;
            $model->onboarding_completed_at = $updatedModel->onboarding_completed_at;
            $model->two_factor_secret = $updatedModel->two_factor_secret;
            $model->two_factor_recovery_codes = $updatedModel->two_factor_recovery_codes;
            $model->two_factor_confirmed_at = $updatedModel->two_factor_confirmed_at;
        }

        $model->save();
    }

    public function findById(Id $id): ?User
    {
        $model = UserModel::query()->find($id->value());

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function findByEmail(Email $email): ?User
    {
        $model = UserModel::query()
            ->where('email', $email->value)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function emailExists(Email $email): bool
    {
        return UserModel::query()
            ->where('email', $email->value)
            ->exists();
    }
}
