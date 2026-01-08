<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Name
{
    private function __construct(
        public string $value
    ) {
        if (empty(trim($value))) {
            throw new InvalidArgumentException('Workspace name cannot be empty');
        }

        if (strlen($value) > 255) {
            throw new InvalidArgumentException('Workspace name cannot exceed 255 characters');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
