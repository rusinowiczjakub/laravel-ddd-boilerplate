<?php

declare(strict_types=1);

namespace Modules\Core\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Core\Command\Contracts\Command;

/**
 * Job wrapper for async command execution.
 *
 * Use this when you need to process a command asynchronously
 * but still need to return an immediate response to the client.
 *
 * Example:
 *   // In controller
 *   $eventId = EventId::create();
 *   ProcessCommandJob::dispatch(new IngestEventCommand($eventId, ...));
 *   return response()->json(['event_id' => $eventId->value()]);
 */
final class ProcessCommandJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;
    public int $backoff = 5;

    public function __construct(
        public readonly Command $command,
    ) {
    }

    public function handle(CommandBus $commandBus): void
    {
        $commandBus->dispatch($this->command);
    }

    /**
     * Get the tags that should be assigned to the job.
     * Useful for Horizon monitoring.
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'command:' . class_basename($this->command),
        ];
    }
}
