<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

readonly class Id extends Uuid
{
    public static function fromString(string $value): static
    {
        return new static($value);
    }
}
