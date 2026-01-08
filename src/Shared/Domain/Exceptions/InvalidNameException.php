<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

final class InvalidNameException extends DomainException
{
    /**
     * @throws InvalidNameException
     */
    public static function invalidLength(int $min, int $max, string $name): self
    {
        throw new self(sprintf(
            'Value must be between %d and %d. Given: %s.',
            $min,
            $max,
            $name
        ));
    }
}
