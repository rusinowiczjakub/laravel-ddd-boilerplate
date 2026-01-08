<?php

declare(strict_types=1);

namespace Modules\Core\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Domain\Exceptions\InvalidUuidException;
use Modules\Shared\Domain\ValueObjects\Id as IdVO;

class AsId implements CastsAttributes
{
    /**
     * @throws InvalidUuidException
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): ?IdVO
    {
        if ($value === null) {
            return null;
        }

        return new IdVO($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if ($value === null) {
            return null;
        }

        return (string) $value;
    }
}
