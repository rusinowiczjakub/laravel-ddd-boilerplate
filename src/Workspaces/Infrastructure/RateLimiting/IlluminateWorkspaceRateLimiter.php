<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\RateLimiting;

use DateMalformedStringException;
use DateTimeImmutable;
use Illuminate\Cache\RateLimiter as IlluminateRateLimiter;
use Modules\Shared\Domain\RateLimiting\RateLimitAction;
use Modules\Shared\Domain\RateLimiting\RateLimiter;
use Modules\Shared\Domain\RateLimiting\RateLimitStatus;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Exception\WorkspaceNotFound;
use Modules\Workspaces\Domain\Models\Plan;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

final readonly class IlluminateWorkspaceRateLimiter implements RateLimiter
{
    public function __construct(
        private IlluminateRateLimiter $limiter,
        private WorkspaceRepository   $workspaceRepository,
    ) {}

    /**
     * @throws WorkspaceNotFound
     * @throws DateMalformedStringException
     */
    public function attempt(WorkspaceId $workspaceId, RateLimitAction $action): RateLimitStatus
    {
        $workspace = $this->workspaceRepository->findById($workspaceId);

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        $key = $this->buildKey($workspaceId, $action);
        $limit = $this->getLimit($workspace->plan(), $action);

        $resetsAt = new DateTimeImmutable('first day of next month');
        $decaySeconds = $resetsAt->getTimestamp() - time();

        $this->limiter->hit($key, $decaySeconds);

        $current = $this->limiter->attempts($key);
        $remaining = $this->limiter->remaining($key, $limit);
        $availableIn = $this->limiter->availableIn($key);
        $availableAt = time() + $availableIn;

        return new RateLimitStatus(
            current: $current,
            limit: $limit,
            exceeded: $current > $limit,
            remaining: max(0, $remaining),
            overage: max(0, $current - $limit),
            resetsAt: new DateTimeImmutable('@' . $availableAt),
        );
    }

    /**
     * @throws WorkspaceNotFound
     */
    public function status(WorkspaceId $workspaceId, RateLimitAction $action): RateLimitStatus
    {
        $workspace = $this->workspaceRepository->findById($workspaceId);

        if (!$workspace) {
            throw new WorkspaceNotFound('Workspace not found');
        }

        $key = $this->buildKey($workspaceId, $action);
        $limit = $this->getLimit($workspace->plan(), $action);

        $current = $this->limiter->attempts($key);
        $remaining = $this->limiter->remaining($key, $limit);
        $availableIn = $this->limiter->availableIn($key);
        $availableAt = time() + $availableIn;

        return new RateLimitStatus(
            current: $current,
            limit: $limit,
            exceeded: $current > $limit,
            remaining: max(0, $remaining),
            overage: max(0, $current - $limit),
            resetsAt: new DateTimeImmutable('@' . $availableAt),
        );
    }

    public function reset(WorkspaceId $workspaceId, RateLimitAction $action): void
    {
        $key = $this->buildKey($workspaceId, $action);

        $this->limiter->clear($key);
    }

    private function buildKey(Id $workspaceId, RateLimitAction $action): string
    {
        $period = date('Y-m');
        return "ratelimit.{$workspaceId->value()}.{$action->value}.{$period}";
    }

    private function getLimit(Plan $plan, RateLimitAction $action): int
    {
        return match($action) {
            RateLimitAction::EVENTS => $plan->eventLimit(),
            RateLimitAction::EMAIL => $plan->emailLimit(),
            RateLimitAction::SMS => $plan->smsLimit(),
            default => PHP_INT_MAX,
        };
    }
}
