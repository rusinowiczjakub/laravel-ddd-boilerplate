<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workspaces;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Workspaces\Application\Commands\TransferOwnershipCommand;

final readonly class TransferOwnership
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request, string $workspaceId): RedirectResponse
    {
        $request->validate([
            'new_owner_id' => ['required', 'string', 'uuid'],
        ]);

        $this->commandBus->dispatch(new TransferOwnershipCommand(
            workspaceId: $workspaceId,
            newOwnerId: $request->input('new_owner_id'),
            currentOwnerId: $request->user()->id->value(),
        ));

        // Redirect to dashboard since user is leaving the workspace
        return redirect()->route('dashboard');
    }
}
