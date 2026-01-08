<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\VerifyEmailCommand;
use Modules\IAM\Domain\Services\PostAuthenticationRedirectResolver;

class VerifyEmailController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly PostAuthenticationRedirectResolver $redirectResolver,
    ) {
    }

    /**
     * Mark the authenticated user's email address as verified.
     */
    public function __invoke(EmailVerificationRequest $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect($this->redirectResolver->resolve($request->user()->id));
        }

        $this->commandBus->dispatch(
            new VerifyEmailCommand(
                userId: $request->user()->id,
            )
        );

        // Refresh the user model to get updated email_verified_at
        $request->user()->refresh();

        event(new Verified($request->user()));

        return redirect($this->redirectResolver->resolve($request->user()->id));
    }
}
