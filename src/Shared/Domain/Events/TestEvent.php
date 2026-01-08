<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Events;

use Modules\Core\Events\Contracts\Event;
use Modules\Core\Events\DomainEvent;
use Modules\Shared\Domain\Exceptions\InvalidUuidException;

class TestEvent extends DomainEvent
{
    public function toPayload(): array
    {
        return [];
    }

    /**
     * @throws InvalidUuidException
     */
    public static function fromPayload(array $payload): Event
    {
        return new TestEvent(
            $payload['aggregate_id'],
            $payload['aggregate_type']
        );
    }
}
