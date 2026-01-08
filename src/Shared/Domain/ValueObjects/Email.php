<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use Modules\Shared\Domain\Exceptions\InvalidEmailException;

final readonly class Email
{
    private string $value;

    /**
     * @throws InvalidEmailException
     */
    public function __construct(
        string $value,
    ) {
        $this->ensureIsValidEmail($value);

        $this->value = mb_strtolower($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $email): bool
    {
        return strtolower($this->value) === strtolower($email->value());
    }

    /**
     * @throws InvalidEmailException
     */
    private function ensureIsValidEmail(string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw InvalidEmailException::invalidFormat($email);
        }
    }
}
