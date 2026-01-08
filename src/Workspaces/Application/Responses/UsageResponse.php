<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

final readonly class UsageResponse
{
    public function __construct(
        public int $eventsUsed,
        public int $eventsLimit,
        public int $notificationsUsed,
        public int $notificationsLimit,
        public int $membersUsed,
        public int $membersLimit,
        public int $workflowsUsed,
        public int $workflowsLimit,
    ) {
    }

    public function eventsPercentage(): int
    {
        if ($this->eventsLimit === PHP_INT_MAX) {
            return 0;
        }

        return $this->eventsLimit > 0
            ? (int) round(($this->eventsUsed / $this->eventsLimit) * 100)
            : 0;
    }

    public function notificationsPercentage(): int
    {
        if ($this->notificationsLimit === PHP_INT_MAX) {
            return 0;
        }

        return $this->notificationsLimit > 0
            ? (int) round(($this->notificationsUsed / $this->notificationsLimit) * 100)
            : 0;
    }

    public function membersPercentage(): int
    {
        if ($this->membersLimit === PHP_INT_MAX) {
            return 0;
        }

        return $this->membersLimit > 0
            ? (int) round(($this->membersUsed / $this->membersLimit) * 100)
            : 0;
    }

    public function workflowsPercentage(): int
    {
        if ($this->workflowsLimit === PHP_INT_MAX) {
            return 0;
        }

        return $this->workflowsLimit > 0
            ? (int) round(($this->workflowsUsed / $this->workflowsLimit) * 100)
            : 0;
    }

    /**
     * @return array{
     *     events: array{used: int, limit: int, percentage: int},
     *     notifications: array{used: int, limit: int, percentage: int},
     *     members: array{used: int, limit: int, percentage: int},
     *     workflows: array{used: int, limit: int, percentage: int}
     * }
     */
    public function toArray(): array
    {
        return [
            'events' => [
                'used' => $this->eventsUsed,
                'limit' => $this->eventsLimit === PHP_INT_MAX ? -1 : $this->eventsLimit,
                'percentage' => $this->eventsPercentage(),
            ],
            'notifications' => [
                'used' => $this->notificationsUsed,
                'limit' => $this->notificationsLimit === PHP_INT_MAX ? -1 : $this->notificationsLimit,
                'percentage' => $this->notificationsPercentage(),
            ],
            'workflows' => [
                'used' => $this->workflowsUsed,
                'limit' => $this->workflowsLimit === PHP_INT_MAX ? -1 : $this->workflowsLimit,
                'percentage' => $this->workflowsPercentage(),
            ],
            'members' => [
                'used' => $this->membersUsed,
                'limit' => $this->membersLimit === PHP_INT_MAX ? -1 : $this->membersLimit,
                'percentage' => $this->membersPercentage(),
            ],
        ];
    }
}
