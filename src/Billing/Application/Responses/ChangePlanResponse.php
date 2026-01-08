<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Responses;

final readonly class ChangePlanResponse
{
    public function __construct(
        public string $action, // 'checkout', 'cancelled', 'scheduled'
        public ?string $checkoutUrl = null, // For upgrades
        public ?string $message = null, // For downgrades/cancellations
    ) {}
}
