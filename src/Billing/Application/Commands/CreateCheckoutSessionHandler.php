<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Commands;

use Modules\Billing\Application\Responses\CheckoutSessionResponse;
use Modules\Billing\Domain\Enums\Plan;
use Modules\Billing\Infrastructure\Services\StripeCheckoutService;
use Modules\Core\Attributes\CommandHandler;
use Modules\Shared\Domain\Exceptions\DomainException;

#[CommandHandler(CreateCheckoutSessionCommand::class)]
final readonly class CreateCheckoutSessionHandler
{
    public function __construct(
        private StripeCheckoutService $checkoutService,
    ) {}

    public function handle(CreateCheckoutSessionCommand $command): CheckoutSessionResponse
    {
        // 1. Validate plan
        $plan = Plan::from($command->plan);

        if (! $plan->isPaid()) {
            throw new DomainException('Cannot create checkout for free plan');
        }

        // 2. Get Stripe price ID (monthly or yearly)
        $priceId = $plan->stripePriceId($command->billingPeriod);

        if (! $priceId) {
            throw new DomainException("Stripe price ID not configured for plan: {$command->plan} ({$command->billingPeriod})");
        }

        // 3. Prepare options
        $options = [
            'plan' => $command->plan, // Required for subscription_data metadata
            'success_url' => $command->successUrl,
            'cancel_url' => $command->cancelUrl,
            'metadata' => [
                'plan' => $command->plan,
                'billing_period' => $command->billingPeriod,
            ],
        ];

        if ($command->couponCode) {
            $options['coupon'] = $command->couponCode;
        }

        // 4. Delegate to infrastructure service
        return $this->checkoutService->createCheckoutSession(
            workspaceId: $command->workspaceId,
            stripePriceId: $priceId,
            options: $options
        );
    }
}
