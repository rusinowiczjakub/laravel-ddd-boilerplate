<?php

declare(strict_types=1);

namespace Modules\Core\Aggregate;

use Modules\Core\Events\Contracts\Event;

abstract class AggregateRoot
{
    protected int $version = 0;

    /** @var Event[] */
    protected array $events = [];

    public function version(): int
    {
        return $this->version;
    }

    public function record(Event $event): void
    {
        $this->version++;

        $this->events[] = $event->withAggregateVersion($this->version);
    }

    public function pullEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }
}
