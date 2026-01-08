<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\Models;

use Modules\Core\Aggregate\AggregateRoot;
use Modules\IAM\Domain\Events\EmailChanged;
use Modules\IAM\Domain\Events\UserRegistered;
use Modules\IAM\Domain\ValueObjects\Email;
use Modules\IAM\Domain\ValueObjects\HashedPassword;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;

class User extends AggregateRoot
{
    private function __construct(
        private Id $id,
        private string $name,
        private Email $email,
        private HashedPassword $password,
        private ?string $firstName,
        private ?string $lastName,
        private ?string $phone,
        private ?Date $emailVerifiedAt,
        private ?Date $phoneVerifiedAt,
        private ?Date $onboardingCompletedAt,
        private ?string $twoFactorSecret,
        private ?array $twoFactorRecoveryCodes,
        private ?Date $twoFactorConfirmedAt,
        private Date $createdAt,
    ) {
    }

    public static function register(
        string $name,
        Email $email,
        HashedPassword $password,
    ): self {
        $user = new self(
            id: Id::create(),
            name: $name,
            email: $email,
            password: $password,
            firstName: null,
            lastName: null,
            phone: null,
            emailVerifiedAt: null,
            phoneVerifiedAt: null,
            onboardingCompletedAt: null,
            twoFactorSecret: null,
            twoFactorRecoveryCodes: null,
            twoFactorConfirmedAt: null,
            createdAt: new Date(),
        );

        $user->record(new UserRegistered(
            userId: $user->id()->value(),
            email: $email->value,
            name: $name,
        ));

        return $user;
    }

    /**
     * @param array{
     *     id: Id,
     *     name: string,
     *     email: Email,
     *     password: HashedPassword,
     *     firstName: string|null,
     *     lastName: string|null,
     *     phone: string|null,
     *     emailVerifiedAt: Date|null,
     *     phoneVerifiedAt: Date|null,
     *     onboardingCompletedAt: Date|null,
     *     twoFactorSecret: string|null,
     *     twoFactorRecoveryCodes: array|null,
     *     twoFactorConfirmedAt: Date|null,
     *     createdAt: Date
     * } $data
     */
    public static function reconstitute(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            email: $data['email'],
            password: $data['password'],
            firstName: $data['firstName'],
            lastName: $data['lastName'],
            phone: $data['phone'],
            emailVerifiedAt: $data['emailVerifiedAt'],
            phoneVerifiedAt: $data['phoneVerifiedAt'],
            onboardingCompletedAt: $data['onboardingCompletedAt'],
            twoFactorSecret: $data['twoFactorSecret'],
            twoFactorRecoveryCodes: $data['twoFactorRecoveryCodes'],
            twoFactorConfirmedAt: $data['twoFactorConfirmedAt'],
            createdAt: $data['createdAt'],
        );
    }

    public function verifyEmail(): void
    {
        $this->emailVerifiedAt = new Date();
    }

    public function verifyPhone(): void
    {
        $this->phoneVerifiedAt = new Date();
    }

    public function completeOnboarding(): void
    {
        $this->onboardingCompletedAt = new Date();
    }

    public function updateProfile(?string $firstName, ?string $lastName, ?string $phone): void
    {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->phone = $phone;
    }

    public function updateName(string $name): void
    {
        $this->name = $name;
    }

    public function changeEmail(Email $newEmail): void
    {
        $oldEmail = $this->email;
        $this->email = $newEmail;
        $this->emailVerifiedAt = null;

        $this->record(new EmailChanged(
            userId: $this->id->value(),
            oldEmail: $oldEmail->value,
            newEmail: $newEmail->value,
        ));
    }

    public function changePassword(HashedPassword $newPassword): void
    {
        $this->password = $newPassword;
    }

    public function verifyPassword(string $plaintext): bool
    {
        return $this->password->verify($plaintext);
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function password(): HashedPassword
    {
        return $this->password;
    }

    public function firstName(): ?string
    {
        return $this->firstName;
    }

    public function lastName(): ?string
    {
        return $this->lastName;
    }

    public function phone(): ?string
    {
        return $this->phone;
    }

    public function emailVerifiedAt(): ?Date
    {
        return $this->emailVerifiedAt;
    }

    public function phoneVerifiedAt(): ?Date
    {
        return $this->phoneVerifiedAt;
    }

    public function onboardingCompletedAt(): ?Date
    {
        return $this->onboardingCompletedAt;
    }

    public function createdAt(): Date
    {
        return $this->createdAt;
    }

    public function hasVerifiedEmail(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function hasCompletedOnboarding(): bool
    {
        return $this->onboardingCompletedAt !== null;
    }

    // Two-Factor Authentication

    public function enableTwoFactor(string $secret, array $recoveryCodes): void
    {
        $this->twoFactorSecret = $secret;
        $this->twoFactorRecoveryCodes = $recoveryCodes;
        $this->twoFactorConfirmedAt = null;
    }

    public function confirmTwoFactor(): void
    {
        $this->twoFactorConfirmedAt = new Date();
    }

    public function disableTwoFactor(): void
    {
        $this->twoFactorSecret = null;
        $this->twoFactorRecoveryCodes = null;
        $this->twoFactorConfirmedAt = null;
    }

    public function regenerateRecoveryCodes(array $recoveryCodes): void
    {
        $this->twoFactorRecoveryCodes = $recoveryCodes;
    }

    public function useRecoveryCode(string $code): bool
    {
        if ($this->twoFactorRecoveryCodes === null) {
            return false;
        }

        $index = array_search($code, $this->twoFactorRecoveryCodes, true);

        if ($index === false) {
            return false;
        }

        unset($this->twoFactorRecoveryCodes[$index]);
        $this->twoFactorRecoveryCodes = array_values($this->twoFactorRecoveryCodes);

        return true;
    }

    public function twoFactorSecret(): ?string
    {
        return $this->twoFactorSecret;
    }

    public function twoFactorRecoveryCodes(): ?array
    {
        return $this->twoFactorRecoveryCodes;
    }

    public function twoFactorConfirmedAt(): ?Date
    {
        return $this->twoFactorConfirmedAt;
    }

    public function hasTwoFactorEnabled(): bool
    {
        return $this->twoFactorSecret !== null && $this->twoFactorConfirmedAt !== null;
    }

    public function hasTwoFactorPending(): bool
    {
        return $this->twoFactorSecret !== null && $this->twoFactorConfirmedAt === null;
    }
}
