<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Controllers;

use Illuminate\Support\Facades\Log;
use Laravel\Cashier\Http\Controllers\WebhookController;
use Modules\Billing\Domain\Events\SubscriptionCreated;
use Modules\Billing\Domain\Events\SubscriptionUpdated;
use Modules\Billing\Domain\Events\SubscriptionDeleted;
use Modules\Core\Events\Contracts\EventBus;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;

/**
 * Handles Stripe webhooks using Laravel Cashier.
 *
 * This extends Cashier's WebhookController which automatically handles:
 * - customer.subscription.created
 * - customer.subscription.updated
 * - customer.subscription.deleted
 * - invoice.payment_succeeded
 * - invoice.payment_failed
 *
 * Custom handlers emit domain events to trigger business logic.
 */
final class HandleStripeWebhook extends WebhookController
{
    /**
     * Handle successful subscription creation.
     *
     * Emits SubscriptionCreated domain event which triggers plan activation.
     */
    protected function handleCustomerSubscriptionCreated(array $payload): void
    {
        $subscription = $payload['data']['object'];
        $customerId = $subscription['customer'] ?? null;

        if (! $customerId) {
            \Log::warning('Subscription created webhook missing customer ID', $payload);

            return;
        }

        // Find workspace by Stripe customer ID
        $workspace = WorkspaceModel::where('stripe_id', $customerId)->first();

        if (! $workspace) {
            \Log::warning('Workspace not found for Stripe customer', [
                'customer_id' => $customerId,
            ]);

            return;
        }

        // Extract plan from metadata
        $plan = $subscription['metadata']['plan'] ?? null;

        if (! $plan) {
            \Log::warning('Subscription created without plan in metadata', $payload);

            return;
        }

        // Extract price ID to determine billing period
        $priceId = $subscription['items']['data'][0]['price']['id'] ?? '';

        // Call parent to let Cashier handle subscription creation in database
        parent::handleCustomerSubscriptionCreated($payload);

        Log::info('sub', $payload);

        // Emit domain event
        $event = new SubscriptionCreated(
            workspaceId: $workspace->id,
            subscriptionId: $subscription['id'],
            plan: $plan,
            status: $subscription['status'],
            stripePriceId: $priceId,
            currentPeriodStart: $subscription['items']['data'][0]['current_period_start'] ?? null,
            currentPeriodEnd: $subscription['items']['data'][0]['current_period_end'] ?? null,
        );

        app(EventBus::class)->dispatch($event);

        \Log::info('Subscription created event emitted', [
            'workspace_id' => $workspace->id,
            'plan' => $plan,
            'status' => $subscription['status'],
        ]);
    }

    /**
     * Handle subscription updates (renewals, cancellations, plan changes).
     *
     * Emits SubscriptionUpdated domain event.
     */
    protected function handleCustomerSubscriptionUpdated(array $payload): void
    {
        $subscription = $payload['data']['object'];
        $customerId = $subscription['customer'] ?? null;

        if (!$customerId) {
            \Log::warning('Subscription updated webhook missing customer ID', $payload);
            return;
        }

        // Find workspace by Stripe customer ID
        $workspace = WorkspaceModel::where('stripe_id', $customerId)->first();

        if (!$workspace) {
            \Log::warning('Workspace not found for Stripe customer', [
                'customer_id' => $customerId,
            ]);
            return;
        }

        // Extract price ID
        $priceId = $subscription['items']['data'][0]['price']['id'] ?? '';

        // Call parent to let Cashier handle subscription updates in database
        parent::handleCustomerSubscriptionUpdated($payload);

        // Emit domain event
        $event = new SubscriptionUpdated(
            workspaceId: $workspace->id,
            subscriptionId: $subscription['id'],
            status: $subscription['status'],
            stripePriceId: $priceId,
            currentPeriodStart: $subscription['items']['data'][0]['current_period_start'] ?? null,
            currentPeriodEnd: $subscription['items']['data'][0]['current_period_end'] ?? null,
            cancelAtPeriodEnd: $subscription['cancel_at_period_end'] ?? false,
            canceledAt: $subscription['canceled_at'] ?? null,
        );

        app(EventBus::class)->dispatch($event);

        \Log::info('Subscription updated event emitted', [
            'workspace_id' => $workspace->id,
            'subscription_id' => $subscription['id'],
            'status' => $subscription['status'],
            'cancel_at_period_end' => $subscription['cancel_at_period_end'] ?? false,
        ]);
    }

    /**
     * Handle subscription deletion (when subscription period ends after cancellation).
     *
     * Emits SubscriptionDeleted domain event which triggers workspace downgrade to free.
     */
    protected function handleCustomerSubscriptionDeleted(array $payload): void
    {
        $subscription = $payload['data']['object'];
        $customerId = $subscription['customer'] ?? null;

        if (!$customerId) {
            \Log::warning('Subscription deleted webhook missing customer ID', $payload);
            return;
        }

        // Find workspace by Stripe customer ID
        $workspace = WorkspaceModel::where('stripe_id', $customerId)->first();

        if (!$workspace) {
            \Log::warning('Workspace not found for Stripe customer on subscription deletion', [
                'customer_id' => $customerId,
            ]);
            return;
        }

        // Call parent to let Cashier handle subscription deletion in database
        parent::handleCustomerSubscriptionDeleted($payload);

        // Emit domain event
        $event = new SubscriptionDeleted(
            workspaceId: $workspace->id,
            subscriptionId: $subscription['id'],
        );

        app(EventBus::class)->dispatch($event);

        \Log::info('Subscription deleted event emitted', [
            'workspace_id' => $workspace->id,
            'subscription_id' => $subscription['id'],
        ]);
    }

    /**
     * Handle successful payments.
     */
    protected function handleInvoicePaymentSucceeded(array $payload): void
    {
        \Log::info('Payment succeeded', [
            'invoice_id' => $payload['data']['object']['id'] ?? null,
            'amount' => $payload['data']['object']['amount_paid'] ?? null,
        ]);
    }
}
