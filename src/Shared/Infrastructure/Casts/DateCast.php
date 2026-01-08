<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Domain\Exceptions\InvalidDateFormatException;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Infrastructure\Exception\InvalidCastValueException;

class DateCast implements CastsAttributes
{
    /**
     * @throws InvalidDateFormatException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Date
    {
        return new Date($value);
    }

    /**
     * @throws InvalidCastValueException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof Date) {
            throw new InvalidCastValueException(sprintf(
                'Value must be an instance of Date, given %s',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value->toString();
    }
}
