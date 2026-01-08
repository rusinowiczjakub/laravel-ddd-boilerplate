<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Responses;

final readonly class BillingPortalSessionResponse
{
    public function __construct(
        public string $portalUrl,
    ) {}
}
