<?php

declare(strict_types=1);

namespace App\Http\Controllers\Invitations;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Workspaces\Application\Commands\AcceptInvitationCommand;
use Modules\Workspaces\Application\Commands\VerifyWorkspaceInvitationCommand;
use Modules\Workspaces\Domain\Exceptions\InvitationAlreadyAcceptedException;
use Modules\Workspaces\Domain\Exceptions\InvitationExpiredException;
use Modules\Workspaces\Domain\Exceptions\InvitationNotFoundException;
use Modules\Workspaces\Domain\Exceptions\InvitationRequiresLoginException;
use Modules\Workspaces\Domain\Exceptions\InvitationRequiresRegistrationException;

final class AcceptInvitation
{
    public function __construct(
        private readonly CommandBus $commandBus,
    ) {
    }

    public function __invoke(string $token, Request $request): RedirectResponse|Response
    {
        // Case 1: User is logged in - accept invitation
        if ($request->user()) {
            try {
                $this->commandBus->dispatch(new AcceptInvitationCommand(
                    token: $token,
                    userId: $request->user()->id->value(),
                ));

                return redirect()->route('dashboard')
                    ->with('success', 'You have successfully joined the workspace!');
            } catch (InvitationNotFoundException $e) {
                return redirect()->route('home')
                    ->with('error', 'Invitation not found.');
            } catch (InvitationExpiredException $e) {
                return redirect()->route('home')
                    ->with('error', 'This invitation has expired.');
            } catch (InvitationAlreadyAcceptedException $e) {
                return redirect()->route('dashboard')
                    ->with('info', 'You are already a member of this workspace.');
            }
        }

        // Case 2 & 3: User not logged in - verify and get redirect instruction via exception
        try {
            $this->commandBus->dispatch(
                new VerifyWorkspaceInvitationCommand(token: $token)
            );

            // Should never reach here - handler always throws
            return redirect()->route('home')->with('success', ' You have joined the workspace!');
        } catch (InvitationRequiresLoginException $e) {
            // Case 2: User has account - redirect to login
            return redirect()->route('login')
                ->with('info', 'Please log in to accept the workspace invitation.');
        } catch (InvitationRequiresRegistrationException $e) {
            // Case 3: User doesn't have account - redirect to register
            return redirect()->route('register')
                ->with('info', 'Please create an account to accept the workspace invitation.');
        } catch (InvitationNotFoundException $e) {
            return redirect()->route('home')
                ->with('error', 'Invitation not found.');
        } catch (InvitationExpiredException $e) {
            return redirect()->route('home')
                ->with('error', 'This invitation has expired.');
        }
    }
}
