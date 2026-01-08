<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Enums;

enum WorkspaceStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case CANCELLED = 'cancelled';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }

    public function isSuspended(): bool
    {
        return $this === self::SUSPENDED;
    }

    public function isCancelled(): bool
    {
        return $this === self::CANCELLED;
    }
}
