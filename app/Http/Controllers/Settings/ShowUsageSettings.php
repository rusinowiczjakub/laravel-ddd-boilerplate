<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\Workspaces\Application\Queries\GetWorkspaceUsageQuery;

final readonly class ShowUsageSettings
{
    public function __construct(
        private QueryBus $queryBus,
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $currentWorkspaceId = session('current_workspace_id');

        $usage = [
            'events' => ['used' => 0, 'limit' => 1000, 'percentage' => 0],
            'notifications' => ['used' => 0, 'limit' => 500, 'percentage' => 0],
            'members' => ['used' => 1, 'limit' => 1, 'percentage' => 100],
        ];

        if ($currentWorkspaceId) {
            $response = $this->queryBus->dispatch(
                new GetWorkspaceUsageQuery($currentWorkspaceId)
            );
            $usage = $response->toArray();
        }

        return Inertia::render('settings/usage', [
            'usage' => $usage,
        ]);
    }
}
