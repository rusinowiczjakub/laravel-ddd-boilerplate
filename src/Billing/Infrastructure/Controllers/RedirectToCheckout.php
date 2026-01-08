<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Billing\Application\Commands\CreateCheckoutSessionCommand;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Shared\Domain\ValueObjects\Uuid;
use Symfony\Component\HttpFoundation\Response;

final readonly class RedirectToCheckout
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(Request $request): Response
    {
        // Get workspace from session, plan from query param or session
        $workspaceId = session('current_workspace_id');
        $plan = $request->query('plan') ?? session('selected_plan', 'starter');

        if (! $workspaceId) {
            return redirect()->route('onboarding.create-workspace')
                ->with('error', 'No workspace found. Please create a workspace first.');
        }

        if (! in_array($plan, ['starter', 'pro'], true)) {
            return redirect()->route('dashboard')
                ->with('error', 'Invalid plan selected: ' . $plan);
        }

        try {
            $response = $this->commandBus->dispatch(new CreateCheckoutSessionCommand(
                workspaceId: new Uuid($workspaceId),
                plan: $plan,
                billingPeriod: 'monthly',
                couponCode: null,
                successUrl: route('dashboard').'?checkout=success',
                cancelUrl: route('pricing'),
            ));

            // Clear session data after successful checkout creation
            session()->forget('selected_plan');

            // External redirect to Stripe Checkout
            return Inertia::location($response->checkoutUrl);
        } catch (\Throwable $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Failed to create checkout session: '.$e->getMessage());
        }
    }
}
