<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

final class InvalidEmailException extends DomainException
{
    /**
     * @throws InvalidEmailException
     */
    public static function invalidFormat(string $email): self
    {
        throw new self(sprintf('Invalid email format: %s.', $email));
    }
}
