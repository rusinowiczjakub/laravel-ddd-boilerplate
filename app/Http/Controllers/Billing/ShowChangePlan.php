<?php

declare(strict_types=1);

namespace App\Http\Controllers\Billing;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Billing\Application\Queries\GetEarlyBirdSlotsQuery;
use Modules\Core\Bus\Contracts\QueryBus;

final readonly class ShowChangePlan
{
    public function __construct(
        private QueryBus $queryBus,
    ) {}

    public function __invoke(Request $request): Response
    {
        // Get current workspace
        $workspaceId = session('current_workspace_id');

        if (! $workspaceId) {
            return Inertia::render('billing/change-plan', [
                'earlyBirdSlots' => ['starter' => 15, 'pro' => 20],
                'error' => 'No workspace selected',
            ]);
        }

        // Get early-bird slots from Stripe
        $earlyBirdSlots = $this->queryBus->dispatch(new GetEarlyBirdSlotsQuery());

        return Inertia::render('billing/change-plan', [
            'earlyBirdSlots' => $earlyBirdSlots->toArray(),
        ]);
    }
}
