<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Billing\Application\Commands\ChangePlanCommand;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Shared\Domain\ValueObjects\Id;
use Symfony\Component\HttpFoundation\Response;

final readonly class ChangePlan
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(Request $request): Response
    {
        // 1. Validate request
        $validated = $request->validate([
            'plan' => 'required|in:free,starter,pro',
            'billing_period' => 'nullable|in:monthly,yearly',
        ]);

        // 2. Get current workspace from session
        $workspaceId = session('current_workspace_id');

        if (! $workspaceId) {
            return back()->with('error', 'No workspace selected');
        }

        // 3. Change plan
        try {
            $response = $this->commandBus->dispatch(new ChangePlanCommand(
                workspaceId: Id::fromString($workspaceId),
                newPlan: $validated['plan'],
                billingPeriod: $validated['billing_period'] ?? 'monthly',
            ));

            // 4. Handle response based on action
            if ($response->action === 'checkout') {
                // Redirect to Stripe Checkout
                return Inertia::location($response->checkoutUrl);
            }

            // For cancelled/scheduled - redirect back with success message
            return redirect()
                ->route('settings.billing')
                ->with('success', $response->message);

        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
