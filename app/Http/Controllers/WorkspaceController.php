<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Workspaces\CreateWorkspaceRequest;
use App\Http\Requests\Workspaces\InviteMembersRequest;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Workspaces\Application\Commands\CreateWorkspaceCommand;
use Modules\Workspaces\Application\Commands\InviteMemberCommand;
use Modules\Workspaces\Application\Responses\WorkspaceCreatedResponse;
use Symfony\Component\HttpFoundation\Response;

final class WorkspaceController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {
    }

    public function store(CreateWorkspaceRequest $request): Response
    {
        $validated = $request->validated();
        $selectedPlan = $validated['plan'] ?? 'free';

        // Always create workspace with FREE plan initially
        // Paid plans are activated after successful Stripe checkout
        /** @var WorkspaceCreatedResponse $response */
        $response = $this->commandBus->dispatch(
            new CreateWorkspaceCommand(
                name: $validated['name'],
                plan: 'free',
                ownerId: $request->user()->id->value(),
            )
        );

        // Set workspace as current
        session(['current_workspace_id' => $response->workspaceId->value()]);

        // Send invitations if provided
        if (!empty($validated['invitations'])) {
            foreach ($validated['invitations'] as $invitation) {
                $this->commandBus->dispatch(
                    new InviteMemberCommand(
                        workspaceId: $response->workspaceId->value(),
                        email: $invitation['email'],
                        role: $invitation['role'],
                        invitedBy: $request->user()->id->value(),
                    )
                );
            }
        }

        // If paid plan selected, redirect to Stripe Checkout
        if ($selectedPlan !== 'free') {
            // Redirect to Stripe checkout with plan as query param
            return redirect()->route('billing.checkout.redirect', ['plan' => $selectedPlan]);
        }

        // Free plan - go straight to dashboard
        return to_route('dashboard')->with('success', 'Workspace created successfully!');
    }

    public function invite(InviteMembersRequest $request, string $workspaceId): RedirectResponse
    {
        $validated = $request->validated();

        foreach ($validated['invitations'] as $invitation) {
            $this->commandBus->dispatch(
                new InviteMemberCommand(
                    workspaceId: $workspaceId,
                    email: $invitation['email'],
                    role: $invitation['role'],
                    invitedBy: $request->user()->id->value(),
                )
            );
        }

        // Redirect to the route specified in the request, or dashboard by default
        $redirectRoute = $request->input('redirect', 'dashboard');

        return to_route($redirectRoute)->with('success', 'Team invitations sent successfully!');
    }
}
