<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Exceptions;

final class InvalidUuidException extends DomainException
{
    /**
     * @throws InvalidUuidException
     */
    public static function invalidUuid(string $uuid): self
    {
        throw new self(sprintf('Invalid uuid provided. Given: %s.', $uuid));
    }
}
