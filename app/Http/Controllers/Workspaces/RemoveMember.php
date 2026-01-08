<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workspaces;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Workspaces\Application\Commands\RemoveMemberCommand;

final readonly class RemoveMember
{
    public function __construct(
        private CommandBus $commandBus,
    ) {
    }

    public function __invoke(Request $request, string $workspaceId, string $memberId): RedirectResponse
    {
        $this->commandBus->dispatch(new RemoveMemberCommand(
            workspaceId: $workspaceId,
            memberId: $memberId,
            removedById: $request->user()->id->value(),
        ));

        return back();
    }
}
