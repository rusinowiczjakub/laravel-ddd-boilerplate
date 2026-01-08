<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Billing\Application\Commands\CreateBillingPortalSessionCommand;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Shared\Domain\ValueObjects\Id;
use Symfony\Component\HttpFoundation\Response;

final readonly class RedirectToBillingPortal
{
    public function __construct(
        private CommandBus $commandBus,
    ) {}

    public function __invoke(Request $request): Response
    {
        // 1. Get current workspace from session
        $workspaceId = session('current_workspace_id');

        if (! $workspaceId) {
            return back()->with('error', 'No workspace selected');
        }

        // 2. Create billing portal session
        try {
            $response = $this->commandBus->dispatch(new CreateBillingPortalSessionCommand(
                workspaceId: Id::fromString($workspaceId),
                returnUrl: route('settings.billing'),
            ));

            // 3. Redirect to Stripe Customer Portal
            return Inertia::location($response->portalUrl);
        } catch (\Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
