<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\ValueObjects;

use Carbon\CarbonImmutable;
use Exception;
use Modules\Shared\Domain\Exceptions\InvalidDateFormatException;

/**
 * @method static Date now($timezone = null)
 * @method Date addSeconds(int $value)
 * @method Date addMinutes(int $value)
 * @method Date addHours(int $value)
 * @method Date addDays(int $value)
 * @method Date subSeconds(int $value)
 * @method Date subMinutes(int $value)
 * @method Date subHours(int $value)
 * @method Date subDays(int $value)
 */
final class Date extends CarbonImmutable
{
    /**
     * @throws InvalidDateFormatException
     */
    public function __construct(mixed $time = 'now', $timezone = null)
    {
        try {
            parent::__construct($time, $timezone);
        } catch (Exception) {
            throw InvalidDateFormatException::invalidDateFormat(self::DEFAULT_TO_STRING_FORMAT, $time);
        }
    }

    public static function fromTimestamp(int $timestamp): self
    {
        return self::createFromTimestamp($timestamp);
    }

    /**
     * @throws InvalidDateFormatException
     */
    public static function fromString(string $value, string $format = self::DEFAULT_TO_STRING_FORMAT): self
    {
        try {
            $date = self::createFromFormat($format, $value);
        } catch (Exception) {
            throw InvalidDateFormatException::invalidDateFormat($format, $value);
        }

        return $date;
    }

    public function toString(string $format = self::DEFAULT_TO_STRING_FORMAT): string
    {
        return $this->format($format);
    }
}
