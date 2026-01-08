<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Adapters;

use Illuminate\Support\Collection as IlluminateCollection;

/**
 * @template TKey of array-key
 * @template TValue
 *
 * @extends IlluminateCollection<TKey, TValue>
 */
class Collection extends IlluminateCollection
{
}
