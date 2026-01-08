<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Slug
{
    private function __construct(
        public string $value
    ) {
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $value)) {
            throw new InvalidArgumentException('Invalid slug format. Only lowercase letters, numbers, and hyphens allowed');
        }

        if (strlen($value) > 100) {
            throw new InvalidArgumentException('Slug cannot exceed 100 characters');
        }
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromName(Name $name): self
    {
        $slug = strtolower($name->value);
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');

        return new self($slug);
    }

    public function toString(): string
    {
        return $this->value;
    }
}
