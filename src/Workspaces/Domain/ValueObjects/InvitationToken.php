<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class InvitationToken
{
    private function __construct(
        public string $value
    ) {
        if (empty($value)) {
            throw new InvalidArgumentException('Invitation token cannot be empty');
        }
    }

    public static function generate(): self
    {
        // Generate secure random token (32 bytes = 64 hex chars)
        $token = bin2hex(random_bytes(32));
        return new self($token);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function equals(InvitationToken $other): bool
    {
        return hash_equals($this->value, $other->value);
    }
}
