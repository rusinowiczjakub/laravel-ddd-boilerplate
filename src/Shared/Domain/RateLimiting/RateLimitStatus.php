<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\RateLimiting;

namespace Modules\Shared\Domain\RateLimiting;

use DateTimeImmutable;

final readonly class RateLimitStatus
{
    public function __construct(
        public int $current,        // Current usage (e.g., 1500 events)
        public int $limit,          // Plan limit (e.g., 1000 events)
        public bool $exceeded,      // Is over limit?
        public int $remaining,      // How many left? (can be negative)
        public int $overage,        // How much over? (0 if not exceeded)
        public DateTimeImmutable $resetsAt,  // When counter resets
    ) {}

    public function isAllowed(): bool
    {
        return !$this->exceeded;
    }

    public function percentageUsed(): float
    {
        if ($this->limit === 0) {
            return 100.0;
        }

        return min(($this->current / $this->limit) * 100, 100.0);
    }

    public function toArray(): array
    {
        return [
            'current' => $this->current,
            'limit' => $this->limit,
            'exceeded' => $this->exceeded,
            'remaining' => $this->remaining,
            'overage' => $this->overage,
            'percentage_used' => $this->percentageUsed(),
            'resets_at' => $this->resetsAt->format('c'),
        ];
    }
}
