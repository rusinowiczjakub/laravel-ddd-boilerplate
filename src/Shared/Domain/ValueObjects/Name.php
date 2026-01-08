<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use Modules\Shared\Domain\Exceptions\InvalidNameException;

readonly class Name
{
    protected const MIN_LENGTH = 2;

    protected const MAX_LENGTH = 55;

    /**
     * @throws InvalidNameException
     */
    public function __construct(
        private string $value,
    ) {
        $this->ensureIsValidName($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $name): bool
    {
        return $this->value === $name->value();
    }

    /**
     * @throws InvalidNameException
     */
    protected function ensureIsValidName(string $name): void
    {
        if (strlen($name) < self::MIN_LENGTH || strlen($name) > self::MAX_LENGTH) {
            InvalidNameException::invalidLength(self::MIN_LENGTH, self::MAX_LENGTH, $name);
        }
    }
}
