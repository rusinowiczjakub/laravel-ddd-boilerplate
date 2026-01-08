<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\SendEmailVerificationCodeCommand;

class EmailVerificationNotificationController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus
    )
    {
    }

    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $this->commandBus->dispatch(new SendEmailVerificationCodeCommand(
            email: $request->user()->email
        ));

        return back()->with('status', 'verification-link-sent');
    }
}
