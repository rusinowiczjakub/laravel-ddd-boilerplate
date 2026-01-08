<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use App\Services\WorkspaceCache;
use Modules\Core\Attributes\CommandHandler;
use Modules\Shared\Domain\Storage\Storage;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

#[CommandHandler(UploadWorkspaceAvatarCommand::class)]
final readonly class UploadWorkspaceAvatarHandler
{
    public function __construct(
        private WorkspaceRepository $workspaceRepository,
        private Storage $storage,
        private WorkspaceCache $workspaceCache,
    ) {
    }

    public function handle(UploadWorkspaceAvatarCommand $command): void
    {
        // Load workspace aggregate
        $workspace = $this->workspaceRepository->findById(
            WorkspaceId::fromString($command->workspaceId)
        );

        if (!$workspace) {
            throw new \RuntimeException('Workspace not found');
        }

        // Delete old avatar if exists
        $oldAvatar = $workspace->avatar();
        if ($oldAvatar) {
            $this->storage->withDisk('public')->delete($oldAvatar);
        }

        // Store new avatar using Storage abstraction
        $extension = $command->avatar->extensionOr('jpg');
        $filename = $command->workspaceId . '.' . $extension;

        $avatarPath = $this->storage
            ->withDisk('public')
            ->putFile(
                directory: 'avatars/workspaces',
                file: $command->avatar,
                filename: $filename,
                options: ['visibility' => 'public']
            );

        // Update workspace aggregate
        $workspace->updateAvatar($avatarPath);

        // Persist
        $this->workspaceRepository->save($workspace);

        // Invalidate cache so avatar shows immediately
        $this->workspaceCache->invalidateWorkspaceSubscription($command->workspaceId);
    }
}
