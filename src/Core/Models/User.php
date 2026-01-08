<?php

namespace Modules\Core\Models;

use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailConcern;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Modules\Core\Builders\User\UserQueryBuilder;
use Modules\Core\Models\Casts\AsId as IdCast;
use Modules\Core\Models\Contracts\BaseModel;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property Id $id
 * @property string $name
 * @property string $email
 * @property ?string $avatar
 * @property string $first_name
 * @property string $last_name
 * @property string $phone
 * @property ?Date $onboarding_completed_at
 * @property ?Date $phone_verified_at
 * @property ?string $two_factor_secret
 * @property ?array $two_factor_recovery_codes
 * @property ?\DateTimeInterface $two_factor_confirmed_at
 */
class User extends BaseModel implements AuthenticatableContract, AuthorizableContract, MustVerifyEmail
{
    use Authenticatable;
    use Authorizable;
    use HasApiTokens;
    use HasFactory;
    use HasUuids;
    use MustVerifyEmailConcern;
    use Notifiable;

    protected $keyType = 'string';

    protected string $guard_name = 'api';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'avatar',
        'first_name',
        'last_name',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'onboarding_completed_at' => 'datetime',
            'two_factor_secret' => 'encrypted',
            'two_factor_recovery_codes' => 'encrypted:array',
            'two_factor_confirmed_at' => 'datetime',
            'id' => IdCast::class,
        ];
    }

    public function newEloquentBuilder($query): Builder
    {
        return new UserQueryBuilder($query);
    }

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->two_factor_secret !== null && $this->two_factor_confirmed_at !== null;
    }

    /**
     * Override Laravel's default email verification notification
     * We use code-based verification instead
     */
    public function sendEmailVerificationNotification(): void
    {
        // Do nothing - we use SendEmailVerificationCodeCommand instead
    }
}
