<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Services;

use Modules\Billing\Application\Responses\CheckoutSessionResponse;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;

/**
 * StripeCheckoutService - Infrastructure service for Stripe Checkout operations.
 *
 * This service encapsulates all Stripe/Cashier interactions, keeping them out of
 * the Application and Domain layers (DDD compliance).
 */
final readonly class StripeCheckoutService
{
    /**
     * Create a Stripe Checkout Session for workspace subscription.
     *
     * @param  Uuid  $workspaceId  The workspace subscribing
     * @param  string  $stripePriceId  Stripe price ID (from Plan enum)
     * @param  array{success_url?: string, cancel_url?: string, coupon?: string, metadata?: array}  $options
     * @return CheckoutSessionResponse
     *
     * @throws \Modules\Shared\Domain\Exceptions\DomainException
     */
    public function createCheckoutSession(
        Uuid $workspaceId,
        string $stripePriceId,
        array $options = []
    ): CheckoutSessionResponse {
        // Extract plan from options (required for webhook)
        $plan = $options['plan'] ?? null;
        if (!$plan) {
            throw new \Modules\Shared\Domain\Exceptions\DomainException('Plan is required for checkout');
        }
        // Load Eloquent model (Infrastructure layer can do this)
        $workspace = WorkspaceModel::find($workspaceId->value());

        if (! $workspace) {
            throw new \Modules\Shared\Domain\Exceptions\DomainException('Workspace not found');
        }

        // Ensure Stripe customer exists
        if (! $workspace->stripe_id) {
            $workspace->createAsStripeCustomer([
                'name' => $workspace->name,
                'metadata' => [
                    'workspace_id' => $workspace->id,
                ],
            ]);
        }

        // Prepare checkout options
        $checkoutOptions = [
            'line_items' => [
                [
                    'price' => $stripePriceId,
                    'quantity' => 1,
                ],
            ],
            'mode' => 'subscription',
            'success_url' => $options['success_url'] ?? route('dashboard'),
            'cancel_url' => $options['cancel_url'] ?? route('pricing'),
            'client_reference_id' => $workspace->id,
            'allow_promotion_codes' => true, // Enable coupon code input in Stripe Checkout
            'metadata' => array_merge([
                'workspace_id' => $workspace->id,
            ], $options['metadata'] ?? []),
            'subscription_data' => [
                'metadata' => [
                    'workspace_id' => $workspace->id,
                    'plan' => $plan, // This is what webhook needs!
                ],
            ],
        ];

        // Add coupon if provided
        if (isset($options['coupon'])) {
            $checkoutOptions['discounts'] = [
                ['coupon' => $options['coupon']],
            ];
        }

        // Create Stripe Checkout Session via Cashier
        $checkoutSession = $workspace->checkout($stripePriceId, $checkoutOptions);

        // Return domain response
        return new CheckoutSessionResponse(
            sessionId: $checkoutSession->id,
            checkoutUrl: $checkoutSession->url,
        );
    }

    /**
     * Create a Billing Portal Session for managing subscription.
     *
     * @param  Uuid  $workspaceId
     * @param  string  $returnUrl
     * @return string Portal URL
     */
    public function createBillingPortalSession(Uuid $workspaceId, string $returnUrl): string
    {
        $workspace = WorkspaceModel::find($workspaceId->value());

        if (! $workspace) {
            throw new \Modules\Shared\Domain\Exceptions\DomainException('Workspace not found');
        }

        if (! $workspace->stripe_id) {
            throw new \Modules\Shared\Domain\Exceptions\DomainException('No Stripe customer found for this workspace');
        }

        return $workspace->billingPortalUrl($returnUrl);
    }
}
