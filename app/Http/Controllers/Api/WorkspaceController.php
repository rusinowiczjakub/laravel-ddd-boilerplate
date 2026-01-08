<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Core\Bus\Contracts\QueryBus;
use Modules\Workspaces\Application\Commands\CreateWorkspaceCommand;
use Modules\Workspaces\Application\Commands\SwitchWorkspaceCommand;
use Modules\Workspaces\Application\Queries\GetCurrentWorkspaceQuery;
use Modules\Workspaces\Application\Queries\GetUserWorkspacesQuery;
use Modules\Workspaces\Domain\Models\Plan;

final class WorkspaceController extends Controller
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $workspaces = $this->queryBus->dispatch(
            new GetUserWorkspacesQuery($request->user()->id->value())
        );

        return response()->json([
            'data' => $workspaces,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'plan' => ['nullable', 'string', 'in:free,starter,pro,enterprise'],
        ]);

        $response = $this->commandBus->dispatch(
            new CreateWorkspaceCommand(
                name: $validated['name'],
                slug: Str::slug($validated['name']),
                plan: $validated['plan'] ?? Plan::FREE,
                ownerId: $request->user()->id->value(),
            )
        );

        return response()->json([
            'workspace' => [
                'id' => $response->workspaceId->value(),
                'name' => $response->name->value,
                'slug' => $response->slug->value,
                'plan' => $response->plan->value,
                'status' => 'active',
                'ownerId' => $request->user()->id,
                'createdAt' => now()->toISOString(),
            ],
            'member' => [
                'id' => $response->memberId->value(),
                'workspaceId' => $response->workspaceId->value(),
                'userId' => $request->user()->id,
                'role' => 'administrator',
                'addedAt' => now()->toISOString(),
            ],
        ], 201);
    }

    public function current(Request $request): JsonResponse
    {
        $workspace = $this->queryBus->dispatch(
            new GetCurrentWorkspaceQuery($request->user()->id->value())
        );

        return response()->json([
            'data' => $workspace ? [
                'id' => $workspace->id()->value(),
                'name' => $workspace->name()->value,
                'slug' => $workspace->slug()->value,
                'plan' => $workspace->plan()->value,
                'status' => $workspace->status()->value,
                'ownerId' => $workspace->ownerId()->value(),
                'createdAt' => $workspace->createdAt()->toISOString(),
            ] : null,
        ]);
    }

    public function switch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'workspace_id' => ['required', 'string', 'uuid'],
        ]);

        $this->commandBus->dispatch(
            new SwitchWorkspaceCommand(
                userId: $request->user()->id->value(),
                workspaceId: $validated['workspace_id'],
            )
        );

        return response()->json([
            'message' => 'Workspace switched successfully',
        ]);
    }
}
