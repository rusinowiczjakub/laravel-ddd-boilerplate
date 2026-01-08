<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Responses;

final readonly class CheckoutSessionResponse
{
    public function __construct(
        public string $sessionId,
        public string $checkoutUrl,
    ) {}
}
