<?php

declare(strict_types=1);

namespace Modules\Core\Collections;

use Illuminate\Support\Collection as IlluminateCollection;

/**
 * Adapter allowing domain collections to use Laravel's Collection features
 * while maintaining clean architecture boundaries.
 */
abstract class Collection extends IlluminateCollection
{
}
