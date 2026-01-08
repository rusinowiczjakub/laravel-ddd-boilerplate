<?php

declare(strict_types=1);

namespace App\Http\Controllers\Workspaces;

use App\Services\WorkspaceCache;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Bus\Contracts\CommandBus;
use Modules\Shared\Domain\Storage\File;
use Modules\Workspaces\Application\Commands\UpdateWorkspaceCommand;
use Modules\Workspaces\Application\Commands\UploadWorkspaceAvatarCommand;
use Modules\Workspaces\Infrastructure\Models\WorkspaceModel;

final readonly class UpdateWorkspace
{
    public function __construct(
        private CommandBus $commandBus,
        private WorkspaceCache $workspaceCache,
    ) {
    }

    public function __invoke(Request $request, string $workspaceId): RedirectResponse
    {
        $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'avatar' => ['sometimes', 'image', 'max:2048'], // 2MB max
        ]);

        // Handle avatar removal
        if ($request->boolean('remove_avatar')) {
            $workspace = WorkspaceModel::find($workspaceId);
            if ($workspace && $workspace->avatar) {
                Storage::disk('public')->delete($workspace->avatar);
                $workspace->avatar = null;
                $workspace->save();

                // Invalidate cache
                $this->workspaceCache->invalidateWorkspaceSubscription($workspaceId);
            }

            return back();
        }

        // Handle name update
        if ($request->has('name')) {
            $this->commandBus->dispatch(new UpdateWorkspaceCommand(
                workspaceId: $workspaceId,
                name: $request->input('name'),
            ));
        }

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $uploadedFile = $request->file('avatar');

            $file = new File(
                path: $uploadedFile->getRealPath(),
                mimeType: $uploadedFile->getMimeType(),
                size: $uploadedFile->getSize(),
                originalName: $uploadedFile->getClientOriginalName(),
            );

            $this->commandBus->dispatch(
                new UploadWorkspaceAvatarCommand(
                    workspaceId: $workspaceId,
                    avatar: $file,
                )
            );
        }

        return back()->with('success', 'Workspace updated successfully!');
    }
}
