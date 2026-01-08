<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Billing\Application\Queries\GetEarlyBirdSlotsQuery;
use Modules\Core\Bus\Contracts\QueryBus;

final class OnboardingController extends Controller
{
    public function __construct(
        private readonly QueryBus $queryBus,
    ) {
    }

    public function createWorkspace(): Response
    {
        return Inertia::render('onboarding/create-workspace');
    }

    public function selectPlan(Request $request): Response
    {
        /** @var \Modules\Billing\Application\Responses\EarlyBirdSlotsResponse $earlyBirdSlots */
        $earlyBirdSlots = $this->queryBus->dispatch(new GetEarlyBirdSlotsQuery());

        return Inertia::render('onboarding/select-plan', [
            'earlyBirdSlots' => [
                'starter' => $earlyBirdSlots->starterSlotsLeft,
                'pro' => $earlyBirdSlots->proSlotsLeft,
            ],
            'workspaceName' => $request->query('name', ''),
            'preselectedPlan' => session('selected_plan'),
        ]);
    }

    public function inviteTeam(Request $request): Response
    {
        return Inertia::render('onboarding/invite-team', [
            'workspaceName' => $request->query('name', ''),
            'plan' => $request->query('plan', 'free'),
        ]);
    }

    public function checkout(Request $request): Response
    {
        $plan = session('selected_plan', $request->query('plan', 'starter'));

        return Inertia::render('onboarding/checkout', [
            'plan' => $plan,
        ]);
    }
}
