<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Exceptions;

use Modules\Shared\Domain\Exceptions\DomainException;

final class InvitationRequiresLoginException extends DomainException
{
    public function __construct(
        public readonly string $email,
        string $message = 'Please log in to accept the invitation',
    ) {
        parent::__construct($message);
    }
}
