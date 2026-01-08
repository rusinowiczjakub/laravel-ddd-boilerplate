<?php

declare(strict_types=1);

namespace Modules\IAM\Infrastructure\Hydrators;

use Modules\Core\Models\User as UserModel;
use Modules\IAM\Domain\Models\User;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Domain\ValueObjects\HashedPassword;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;

final readonly class UserHydrator
{
    public function toDomain(UserModel $model): User
    {
        return User::reconstitute([
            'id' => $model->id,
            'name' => $model->name,
            'email' => Email::fromString($model->email),
            'password' => HashedPassword::fromHash($model->password),
            'firstName' => $model->first_name,
            'lastName' => $model->last_name,
            'phone' => $model->phone,
            'emailVerifiedAt' => $model->email_verified_at ? new Date($model->email_verified_at) : null,
            'phoneVerifiedAt' => $model->phone_verified_at ? new Date($model->phone_verified_at) : null,
            'onboardingCompletedAt' => $model->onboarding_completed_at ? new Date($model->onboarding_completed_at) : null,
            'twoFactorSecret' => $model->two_factor_secret,
            'twoFactorRecoveryCodes' => $model->two_factor_recovery_codes,
            'twoFactorConfirmedAt' => $model->two_factor_confirmed_at ? new Date($model->two_factor_confirmed_at) : null,
            'createdAt' => new Date($model->created_at),
        ]);
    }

    public function toModel(User $user): UserModel
    {
        $model = new UserModel();
        $model->id = $user->id()->value();
        $model->name = $user->name();
        $model->email = $user->email()->value;
        $model->password = $user->password()->hash;
        $model->first_name = $user->firstName();
        $model->last_name = $user->lastName();
        $model->phone = $user->phone();
        $model->email_verified_at = $user->emailVerifiedAt();
        $model->phone_verified_at = $user->phoneVerifiedAt();
        $model->onboarding_completed_at = $user->onboardingCompletedAt();
        $model->two_factor_secret = $user->twoFactorSecret();
        $model->two_factor_recovery_codes = $user->twoFactorRecoveryCodes();
        $model->two_factor_confirmed_at = $user->twoFactorConfirmedAt();
        $model->created_at = $user->createdAt();

        return $model;
    }
}
