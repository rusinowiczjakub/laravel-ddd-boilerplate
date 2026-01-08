<?php

declare(strict_types=1);

namespace Modules\IAM\Domain\ValueObjects;

use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

final readonly class HashedPassword
{
    private function __construct(
        public string $hash
    ) {
        if (empty($hash)) {
            throw new InvalidArgumentException('Password hash cannot be empty');
        }
    }

    public static function fromPlaintext(string $plaintext): self
    {
        if (strlen($plaintext) < 8) {
            throw new InvalidArgumentException('Password must be at least 8 characters');
        }

        return new self(Hash::make($plaintext));
    }

    public static function fromHash(string $hash): self
    {
        return new self($hash);
    }

    public function verify(string $plaintext): bool
    {
        return Hash::check($plaintext, $this->hash);
    }

    public function toString(): string
    {
        return $this->hash;
    }
}
