<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Responses;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\ValueObjects\Email;
use Modules\Workspaces\Domain\ValueObjects\InvitationToken;

final readonly class InvitationCreatedResponse
{
    public function __construct(
        public Id $invitationId,
        public Email $email,
        public InvitationToken $token,
        public ?Date $expiresAt,
    ) {
    }
}
