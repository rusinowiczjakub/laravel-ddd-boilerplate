<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\ChangeEmailCommand;
use Modules\IAM\Domain\Exceptions\EmailAlreadyExistsException;
use Modules\IAM\Domain\Exceptions\InvalidCredentialsException;

final readonly class ChangeEmail
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'string'],
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        try {
            $this->commandBus->dispatch(new ChangeEmailCommand(
                userId: $request->user()->id->value(),
                password: $request->input('password'),
                newEmail: $request->input('email'),
            ));

            return back();
        } catch (InvalidCredentialsException) {
            throw ValidationException::withMessages([
                'password' => ['The provided password is incorrect.'],
            ]);
        } catch (EmailAlreadyExistsException) {
            throw ValidationException::withMessages([
                'email' => ['This email is already in use.'],
            ]);
        }
    }
}
