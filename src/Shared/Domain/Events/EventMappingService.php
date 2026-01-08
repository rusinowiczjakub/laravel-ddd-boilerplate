<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Events;

use Modules\Core\Events\Contracts\Event;
use Modules\Shared\Domain\Events\Contracts\DomainEventMapper;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;

final readonly class EventMappingService
{
    /**
     * @var DomainEventMapper[]
     */
    private array $mappers;

    public function __construct(
        DomainEventMapper ...$mappers
    ) {
        $this->mappers = $mappers;
    }

    /**
     * Map domain event to integration event if mapper exists
     */
    public function mapToIntegrationEvent(Event $domainEvent): ?IntegrationEvent
    {
        foreach ($this->mappers as $mapper) {
            if ($mapper->canMap($domainEvent)) {
                return $mapper->map($domainEvent);
            }
        }

        return null;
    }
}
