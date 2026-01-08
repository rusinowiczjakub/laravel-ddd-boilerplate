<?php

declare(strict_types=1);

namespace App\Http\Controllers\TwoFactor;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\ConfirmTwoFactorCommand;
use Modules\IAM\Domain\Exceptions\InvalidTwoFactorCodeException;

final readonly class ConfirmTwoFactor
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
        ]);

        try {
            $this->commandBus->dispatch(new ConfirmTwoFactorCommand(
                userId: $request->user()->id->value(),
                code: $request->input('code'),
            ));

            return back();
        } catch (InvalidTwoFactorCodeException) {
            throw ValidationException::withMessages([
                'code' => ['The provided code is invalid.'],
            ]);
        }
    }
}
