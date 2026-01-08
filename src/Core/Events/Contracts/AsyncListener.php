<?php

declare(strict_types=1);

namespace Modules\Core\Events\Contracts;

/**
 * Marker interface for listeners that should be processed asynchronously
 */
interface AsyncListener
{
    public function viaQueue(): string;
}
