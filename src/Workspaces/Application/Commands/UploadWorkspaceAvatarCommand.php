<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Command\Contracts\Command;
use Modules\Shared\Domain\Storage\File;

final readonly class UploadWorkspaceAvatarCommand implements Command
{
    public function __construct(
        public string $workspaceId,
        public File $avatar,
    ) {
    }
}
