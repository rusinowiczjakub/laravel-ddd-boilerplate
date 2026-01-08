<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Listeners;

use Modules\Core\Attributes\Subscribe;
use Modules\Core\Events\Contracts\AsyncListener;
use Modules\Shared\Domain\Events\EventLimitWarningIntegrationEvent;
use Modules\Shared\Domain\Models\Queues;

#[Subscribe(EventLimitWarningIntegrationEvent::class)]
class NotifyUserOnPlanLimitExceeded implements AsyncListener
{
    public function __invoke()
    {
        // TODO: Implement __invoke() method.
    }

    public function viaQueue(): string
    {
        return Queues::APP_NOTIFICATIONS->value;
    }
}
