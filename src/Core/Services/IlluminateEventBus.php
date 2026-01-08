<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use Illuminate\Events\Dispatcher;
use Modules\Core\Events\Contracts\Event;
use Modules\Core\Events\Contracts\EventBus;
use Modules\Shared\Domain\Events\EventMappingService;

readonly class IlluminateEventBus implements EventBus
{
    public function __construct(
        private Dispatcher $dispatcher,
        private ?EventMappingService $eventMappingService = null
    ) {
    }

    public function dispatch(Event ...$events): void
    {
        foreach ($events as $event) {
            // Dispatch original domain event (for intra-module listeners)
            $this->dispatcher->dispatch($event);

            // Map to integration event if mapper exists and dispatch
            // (for cross-module communication)
            if ($this->eventMappingService !== null) {
                $integrationEvent = $this->eventMappingService->mapToIntegrationEvent($event);
                if ($integrationEvent !== null) {
                    $this->dispatcher->dispatch($integrationEvent);
                }
                // No warning - not every domain event needs cross-boundary integration
            }
        }
    }
}
