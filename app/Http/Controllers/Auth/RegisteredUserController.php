<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Core\Models\User;
use Modules\IAM\Application\Commands\RegisterUserCommand;
use Modules\Workspaces\Domain\Services\MemberInvitationSessionManager;

class RegisteredUserController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly MemberInvitationSessionManager $sessionManager,
    ) {
    }

    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        // Store selected plan in session if coming from pricing page
        if (request()->has('plan')) {
            session()->put('selected_plan', request()->query('plan'));
        }

        return Inertia::render('auth/register', [
            'invitationEmail' => $this->sessionManager->getPendingInvitationEmail(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Validate email matches invitation if present
        $invitationEmail = $this->sessionManager->getPendingInvitationEmail();
        if ($invitationEmail && $validated['email'] !== $invitationEmail) {
            return back()->withErrors([
                'email' => 'Email must match the invitation email: ' . $invitationEmail,
            ]);
        }

        $response = $this->commandBus->dispatch(
            new RegisterUserCommand(
                name: $validated['name'],
                email: $validated['email'],
                password: $validated['password'],
            )
        );

        // Find the Eloquent model for Laravel Auth
        $user = User::find($response->userId->value());

        Auth::login($user);

        return to_route('verification.notice');
    }
}
