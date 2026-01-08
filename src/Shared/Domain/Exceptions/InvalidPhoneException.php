<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

final class InvalidPhoneException extends DomainException
{
    public static function invalidFormat(string $phone): self
    {
        return new self(sprintf('Invalid phone number format: %s.', $phone));
    }

    public static function missingCountryCode(string $phone): self
    {
        return new self(sprintf('Phone number must start with "+". Given: %s.', $phone));
    }
}
