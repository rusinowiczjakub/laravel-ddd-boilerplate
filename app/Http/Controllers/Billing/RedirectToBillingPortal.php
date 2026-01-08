<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;
use Symfony\Component\HttpFoundation\Response;

final readonly class RedirectToBillingPortal
{
    public function __invoke(Request $request): Response
    {
        $workspaceId = session('current_workspace_id');

        if (!$workspaceId) {
            return back()->with('error', 'No workspace selected');
        }

        $workspace = WorkspaceModel::find($workspaceId);

        if (!$workspace) {
            return back()->with('error', 'Workspace not found');
        }

        if (!$workspace->hasStripeId()) {
            return back()->with('error', 'No billing account found. Please subscribe to a plan first.');
        }

        // Get the billing portal URL and use Inertia::location for external redirect
        $portalUrl = $workspace->billingPortalUrl(route('settings.billing'));

        return Inertia::location($portalUrl);
    }
}
