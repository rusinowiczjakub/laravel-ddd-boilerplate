<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workspaces;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Workspaces\Application\Commands\CancelInvitationCommand;

final readonly class CancelInvitation
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request, string $workspaceId, string $invitationId): RedirectResponse
    {
        $this->commandBus->dispatch(new CancelInvitationCommand(
            workspaceId: $workspaceId,
            invitationId: $invitationId,
            cancelledById: $request->user()->id->value(),
        ));

        return back();
    }
}
