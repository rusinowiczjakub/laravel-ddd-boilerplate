<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\Models;

enum Queues: string
{
    case DEFAULT = 'default';
    case WORKFLOWS = 'workflows';
    case APP_NOTIFICATIONS = 'app_notifications';
}
