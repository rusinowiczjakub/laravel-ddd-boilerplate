<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use JsonSerializable;
use Modules\Shared\Domain\Exceptions\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;

readonly class Uuid implements JsonSerializable
{
    /**
     * @throws InvalidUuidException
     */
    final public function __construct(private string $value)
    {
        $this->validate($value);
    }

    /**
     * @throws InvalidUuidException
     */
    public static function create(): static
    {
        return new static(RamseyUuid::uuid4()->toString());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $uuid): bool
    {
        return $this->value === $uuid->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    public function jsonSerialize(): string
    {
        return $this->value();
    }

    /**
     * @throws InvalidUuidException
     */
    private function validate(string $uuid): void
    {
        if (! RamseyUuid::isValid($uuid)) {
            throw InvalidUuidException::invalidUuid($uuid);
        }
    }
}
