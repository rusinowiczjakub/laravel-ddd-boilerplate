<?php

declare(strict_types=1);

namespace Modules\Core\Events;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Events\Contracts\Event;

/**
 * Infrastructure wrapper that makes domain listeners queueable
 */
final class QueueableListenerWrapper implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly string $listenerClass,
        private readonly Event $event,
    ) {
    }

    public function handle(): void
    {
        $listener = app()->make($this->listenerClass);
        $listener($this->event);
    }
}
