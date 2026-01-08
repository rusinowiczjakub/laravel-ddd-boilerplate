<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\ConfirmPasswordCommand;
use Modules\IAM\Domain\Exceptions\InvalidCredentialsException;

class ConfirmablePasswordController extends Controller
{
    /**
     * Show the confirm password page.
     */
    public function show(): Response
    {
        return Inertia::render('auth/confirm-password');
    }

    /**
     * Confirm the user's password.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $commandBus = app(CommandBus::class);
            $commandBus->dispatch(
                new ConfirmPasswordCommand(
                    userId: $request->user()->id,
                    password: $request->password,
                )
            );

            $request->session()->put('auth.password_confirmed_at', time());

            return redirect()->intended(route('dashboard', absolute: false));
        } catch (InvalidCredentialsException) {
            throw ValidationException::withMessages([
                'password' => __('auth.password'),
            ]);
        }
    }
}
