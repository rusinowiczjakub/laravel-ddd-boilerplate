<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Exceptions;

use Modules\Shared\Domain\Exceptions\DomainException;

final class UnauthorizedWorkspaceAccessException extends DomainException
{
}
