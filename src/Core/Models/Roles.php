<?php

declare(strict_types=1);

namespace Modules\Core\Models;

enum Roles: string
{
    case VENDOR = 'vendor';
    case CLIENT = 'client';
    case ADMIN = 'admin';
}
