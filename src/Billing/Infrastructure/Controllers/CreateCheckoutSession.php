<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Billing\Application\Commands\CreateCheckoutSessionCommand;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateCheckoutSession
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(Request $request): Response
    {
        // 1. Validate request
        $validated = $request->validate([
            'plan' => 'required|in:starter,pro',
            'billing_period' => 'nullable|in:monthly,yearly',
            'coupon' => 'nullable|string|max:50',
        ]);

        // 2. Get current workspace from session
        $workspaceId = session('current_workspace_id');

        if (! $workspaceId) {
            return back()->with('error', 'No workspace selected');
        }

        // 3. Create checkout session
        try {
            $response = $this->commandBus->dispatch(new CreateCheckoutSessionCommand(
                workspaceId: Id::fromString($workspaceId),
                plan: $validated['plan'],
                billingPeriod: $validated['billing_period'] ?? 'monthly',
                couponCode: $validated['coupon'] ?? null,
                successUrl: route('dashboard').'?checkout=success',
                cancelUrl: route('pricing'),
            ));

            // 4. External redirect to Stripe Checkout using Inertia
            return Inertia::location($response->checkoutUrl);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
