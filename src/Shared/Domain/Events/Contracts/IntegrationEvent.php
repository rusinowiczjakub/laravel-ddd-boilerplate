<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Events\Contracts;

use Modules\Core\Events\Contracts\Event;

/**
 * Marker interface for integration events that cross domain boundaries
 */
interface IntegrationEvent extends Event
{
    public function key(): string;
}
