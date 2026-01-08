<?php

declare(strict_types=1);

namespace Modules\Core\Models\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Modules\Shared\Domain\ValueObjects\Date as DateModel;

class AsDate implements CastsAttributes
{
    public function get(Model $model, string $key, mixed $value, array $attributes): DateModel
    {
        return DateModel::createFromTimestamp($value);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): int
    {
        return $value->timestamp;
    }
}
