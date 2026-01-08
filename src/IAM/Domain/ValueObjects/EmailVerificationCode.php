<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class EmailVerificationCode
{
    private function __construct(
        private string $value,
    ) {
        if (strlen($value) !== 8) {
            throw new InvalidArgumentException('Verification code must be 8 characters long');
        }

        if (!ctype_alnum($value)) {
            throw new InvalidArgumentException('Verification code must be alphanumeric');
        }
    }

    public static function generate(): self
    {
        // Generate 8-character alphanumeric code (podobnie jak Nightwatch)
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Bez mylących znaków
        $code = '';

        for ($i = 0; $i < 8; $i++) {
            $code .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return new self($code);
    }

    public static function fromString(string $value): self
    {
        return new self(strtoupper($value));
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
