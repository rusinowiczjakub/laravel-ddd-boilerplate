<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\Workspaces\Application\Queries\GetWorkspaceMembersQuery;

final readonly class ShowWorkspaceSettings
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $currentWorkspaceId = session('current_workspace_id');

        $members = [];
        if ($currentWorkspaceId) {
            $response = $this->queryBus->dispatch(
                new GetWorkspaceMembersQuery($currentWorkspaceId)
            );
            $members = $response->toArray()['members'];
        }

        return Inertia::render('settings/workspace', [
            'members' => $members,
        ]);
    }
}
