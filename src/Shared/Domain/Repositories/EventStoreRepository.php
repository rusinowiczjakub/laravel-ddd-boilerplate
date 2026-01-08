<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Repositories;

use Modules\Core\Events\Contracts\Event;

interface EventStoreRepository
{
    public function store(Event $event): void;
}
