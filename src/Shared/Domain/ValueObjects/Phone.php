<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use Modules\Shared\Domain\Exceptions\InvalidPhoneException;

final readonly class Phone
{
    private const PHONE_REGEX = '/^\+[1-9]\d{6,14}$/';

    private string $value;

    /**
     * @throws InvalidPhoneException
     */
    public function __construct(string $phone)
    {
        $this->value = $this->normalizeAndValidate($phone);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $phone): bool
    {
        return $this->value === $phone->value();
    }

    /**
     * @throws InvalidPhoneException
     */
    private function normalizeAndValidate(string $phone): string
    {
        $normalized = preg_replace('/[\s\-().]/', '', $phone);

        if (! str_starts_with($normalized, '+')) {
            throw InvalidPhoneException::missingCountryCode($phone);
        }

        if (! preg_match(self::PHONE_REGEX, $normalized)) {
            throw InvalidPhoneException::invalidFormat($phone);
        }

        return $normalized;
    }
}
