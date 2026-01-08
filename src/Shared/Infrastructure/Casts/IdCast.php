<?php

declare(strict_types=1);

namespace Modules\Shared\Infrastructure\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Domain\Exceptions\InvalidUuidException;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Shared\Infrastructure\Exception\InvalidCastValueException;

class IdCast implements CastsAttributes
{
    /**
     * @throws InvalidUuidException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): Id
    {
        return new Id($value);
    }

    /**
     * @throws InvalidCastValueException
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! $value instanceof Id) {
            throw new InvalidCastValueException(sprintf(
                'Value must be an instance of Id, given %s',
                is_object($value) ? get_class($value) : gettype($value)
            ));
        }

        return $value->value();
    }
}
