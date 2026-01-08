<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\ChangePasswordCommand;
use Modules\IAM\Domain\Exceptions\InvalidCredentialsException;

final readonly class UpdatePassword
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        try {
            $this->commandBus->dispatch(new ChangePasswordCommand(
                userId: $request->user()->id->value(),
                currentPassword: $request->input('current_password'),
                newPassword: $request->input('password'),
            ));

            return back();
        } catch (InvalidCredentialsException) {
            throw ValidationException::withMessages([
                'current_password' => ['The provided password is incorrect.'],
            ]);
        }
    }
}
