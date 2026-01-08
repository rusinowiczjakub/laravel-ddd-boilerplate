<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Framework\Laravel\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Modules\Billing\Domain\Repositories\BillingWorkspaceRepository;
use Modules\Billing\Domain\Services\SubscriptionService;
use Modules\Billing\Infrastructure\Controllers\ChangePlan;
use Modules\Billing\Infrastructure\Controllers\CreateCheckoutSession;
use Modules\Billing\Infrastructure\Controllers\HandleStripeWebhook;
use Modules\Billing\Infrastructure\Controllers\RedirectToBillingPortal;
use Modules\Billing\Infrastructure\Controllers\RedirectToCheckout;
use Modules\Billing\Infrastructure\Repositories\EloquentBillingWorkspaceRepository;
use Modules\Billing\Infrastructure\Services\StripeSubscriptionService;

final class BillingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repository
        $this->app->bind(
            BillingWorkspaceRepository::class,
            EloquentBillingWorkspaceRepository::class,
        );

        // Bind subscription service
        $this->app->bind(
            SubscriptionService::class,
            StripeSubscriptionService::class,
        );
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        // Change Plan route (authenticated)
        Route::middleware(['web', 'auth'])
            ->post('/billing/change-plan', ChangePlan::class)
            ->name('billing.change-plan.post');

        // Checkout route (authenticated) - POST for API/forms
        Route::middleware(['web', 'auth'])
            ->post('/billing/checkout', CreateCheckoutSession::class)
            ->name('billing.checkout');

        // Checkout redirect route (authenticated) - GET for onboarding redirect
        Route::middleware(['web', 'auth'])
            ->get('/billing/checkout/redirect', RedirectToCheckout::class)
            ->name('billing.checkout.redirect');

        // Billing Portal route (authenticated)
        Route::middleware(['web', 'auth'])
            ->post('/billing/portal', RedirectToBillingPortal::class)
            ->name('billing.portal');

        // Webhook route (public, verified by Stripe signature)
        Route::post(
            '/billing/webhook',
            [HandleStripeWebhook::class, 'handleWebhook']
        )->name('billing.webhook');
    }
}
