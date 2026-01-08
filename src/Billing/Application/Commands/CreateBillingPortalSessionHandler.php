<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Billing\Application\Responses\BillingPortalSessionResponse;
use Modules\Billing\Infrastructure\Services\StripeCheckoutService;
use Modules\Core\Attributes\CommandHandler;

#[CommandHandler(CreateBillingPortalSessionCommand::class)]
final readonly class CreateBillingPortalSessionHandler
{
    public function __construct(
        private StripeCheckoutService $checkoutService,
    ) {}

    public function handle(CreateBillingPortalSessionCommand $command): BillingPortalSessionResponse
    {
        // Delegate to infrastructure service
        $sessionUrl = $this->checkoutService->createBillingPortalSession(
            workspaceId: $command->workspaceId,
            returnUrl: $command->returnUrl
        );

        return new BillingPortalSessionResponse(
            portalUrl: $sessionUrl,
        );
    }
}
