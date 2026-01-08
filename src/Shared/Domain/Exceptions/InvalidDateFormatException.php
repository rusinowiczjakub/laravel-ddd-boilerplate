<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

final class InvalidDateFormatException extends DomainException
{
    /**
     * @throws InvalidDateFormatException
     */
    public static function invalidDateFormat(string $format, string $value): self
    {
        throw new self(sprintf('Invalid date format provided. Expected: %s. Given: %s.', $format, $value));
    }
}
