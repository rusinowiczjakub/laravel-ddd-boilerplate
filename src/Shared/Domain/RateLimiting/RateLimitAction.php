<?php

declare(strict_types=1);

namespace Modules\Shared\Domain\RateLimiting;

enum RateLimitAction: string
{
    case EVENTS = 'events';
    case WORKFLOWS = 'workflows';
    case API_REQUESTS = 'api_requests';
}
