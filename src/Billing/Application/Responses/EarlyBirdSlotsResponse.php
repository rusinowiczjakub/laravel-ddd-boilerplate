<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Responses;

final readonly class EarlyBirdSlotsResponse
{
    public function __construct(
        public int $starterSlotsLeft,
        public int $proSlotsLeft,
    ) {}

    public function toArray(): array
    {
        return [
            'starter' => $this->starterSlotsLeft,
            'pro' => $this->proSlotsLeft,
        ];
    }
}
