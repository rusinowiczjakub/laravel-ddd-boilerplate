<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class ShowBillingSettings
{
    public function __invoke(Request $request): Response
    {
        // TODO: Integrate with Stripe for real billing data
        // For now, return basic plan info from shared currentWorkspace

        return Inertia::render('settings/billing', [
            // Billing data will come from Stripe integration
            // Currently using shared currentWorkspace from middleware
        ]);
    }
}
