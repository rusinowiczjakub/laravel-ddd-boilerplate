<?php

declare(strict_types=1);

namespace Modules\Shared\Application\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Modules\Core\Attributes\Subscribe;
use Modules\Core\Events\Contracts\Event;
use Modules\Shared\Domain\Events\Contracts\IntegrationEvent;
use Modules\Shared\Domain\Repositories\EventStoreRepository;

#[Subscribe(target: Event::class)]
class StoreDomainEvent implements ShouldQueue // @TODO: find solution to remove Illuminate import here
{
    public function __construct(
        private readonly EventStoreRepository $eventStoreRepository
    ) {
    }

    public function __invoke(Event $event): void
    {
        if ($event instanceof IntegrationEvent) {
            return;
        }

        $this->eventStoreRepository->store($event);
    }
}
