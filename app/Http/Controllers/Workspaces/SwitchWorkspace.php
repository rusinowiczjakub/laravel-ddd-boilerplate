<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workspaces;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final class SwitchWorkspace
{
    public function __invoke(string $workspaceId, Request $request): RedirectResponse
    {
        // TODO: Verify user has access to this workspace
        // TODO: Store selected workspace in session or user preferences

        session(['current_workspace_id' => $workspaceId]);

        return redirect()->back()->with('success', 'Workspace switched successfully');
    }
}
