<?php

declare(strict_types=1);

namespace Modules\Billing\Application\Queries;

use Modules\Billing\Application\Responses\EarlyBirdSlotsResponse;
use Modules\Core\Attributes\QueryHandler;
use Stripe\StripeClient;

#[QueryHandler(GetEarlyBirdSlotsQuery::class)]
final readonly class GetEarlyBirdSlotsHandler
{
    private StripeClient $stripe;

    public function __construct()
    {
        $this->stripe = new StripeClient(config('services.stripe.secret'));
    }

    public function handle(GetEarlyBirdSlotsQuery $query): EarlyBirdSlotsResponse
    {
        try {
            // Get Starter coupon
            $starterCoupon = $this->stripe->coupons->retrieve('EARLYBIRD_STARTER');
            $starterTotal = $starterCoupon->max_redemptions ?? 15;
            $starterUsed = $starterCoupon->times_redeemed ?? 0;
            $starterLeft = max(0, $starterTotal - $starterUsed);
        } catch (\Throwable $e) {
            // Coupon doesn't exist yet or API error - use default
            $starterLeft = 15;
        }

        try {
            // Get Pro coupon
            $proCoupon = $this->stripe->coupons->retrieve('EARLYBIRD_PRO');
            $proTotal = $proCoupon->max_redemptions ?? 20;
            $proUsed = $proCoupon->times_redeemed ?? 0;
            $proLeft = max(0, $proTotal - $proUsed);
        } catch (\Throwable $e) {
            // Coupon doesn't exist yet or API error - use default
            $proLeft = 20;
        }

        return new EarlyBirdSlotsResponse(
            starterSlotsLeft: $starterLeft,
            proSlotsLeft: $proLeft,
        );
    }
}
