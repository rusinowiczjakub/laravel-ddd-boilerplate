<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workspaces;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Workspaces\Application\Commands\ChangeMemberRoleCommand;

final readonly class ChangeMemberRole
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request, string $workspaceId, string $memberId): RedirectResponse
    {
        $request->validate([
            'role' => ['required', 'string', 'in:administrator,collaborator'],
        ]);

        $this->commandBus->dispatch(new ChangeMemberRoleCommand(
            workspaceId: $workspaceId,
            memberId: $memberId,
            role: $request->input('role'),
            changedById: $request->user()->id->value(),
        ));

        return back();
    }
}
