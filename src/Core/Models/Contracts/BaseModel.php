<?php

declare(strict_types=1);

namespace Modules\Core\Models\Contracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use LogicException;

abstract class BaseModel extends Model
{
    //    public function newEloquentBuilder($query): Builder
    //    {
    //        throw new LogicException(sprintf('Model %s must define `newEloquentBuilder', static::class));
    //    }
}
