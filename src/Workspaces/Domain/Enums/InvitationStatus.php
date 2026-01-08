<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Enums;

enum InvitationStatus: string
{
    case PENDING = 'pending';
    case ACCEPTED = 'accepted';
    case CANCELLED = 'cancelled';
}
