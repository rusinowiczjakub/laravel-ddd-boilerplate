<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Core\Command\Contracts\Command;
use Modules\Shared\Domain\ValueObjects\Uuid;

final readonly class CreateCheckoutSessionCommand implements Command
{
    public function __construct(
        public Uuid $workspaceId,
        public string $plan, // 'starter' or 'pro'
        public string $billingPeriod = 'monthly', // 'monthly' or 'yearly'
        public ?string $couponCode = null, // Early-bird coupon
        public ?string $successUrl = null,
        public ?string $cancelUrl = null,
    ) {}
}
