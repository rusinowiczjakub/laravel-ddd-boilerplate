<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Events\Contracts;

use Modules\Core\Events\Contracts\Event;

interface DomainEventMapper
{
    /**
     * Check if this mapper can map the given domain event
     */
    public function canMap(Event $domainEvent): bool;

    /**
     * Map domain event to integration event
     */
    public function map(Event $event): IntegrationEvent;
}
