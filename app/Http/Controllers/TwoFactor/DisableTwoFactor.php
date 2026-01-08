<?php

declare(strict_types=1);

namespace App\Http\Controllers\TwoFactor;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\IAM\Application\Commands\DisableTwoFactorCommand;

final readonly class DisableTwoFactor
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request): RedirectResponse
    {
        $this->commandBus->dispatch(new DisableTwoFactorCommand(
            userId: $request->user()->id->value(),
        ));

        return back();
    }
}
