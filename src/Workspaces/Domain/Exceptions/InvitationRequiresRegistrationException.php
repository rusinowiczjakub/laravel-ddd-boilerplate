<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Exceptions;

use Modules\Shared\Domain\Exceptions\DomainException;

final class InvitationRequiresRegistrationException extends DomainException
{
    public function __construct(
        public readonly string $email,
        string $message = 'Please create an account to accept the invitation',
    ) {
        parent::__construct($message);
    }
}
