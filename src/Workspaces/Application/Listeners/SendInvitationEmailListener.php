<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Listeners;

use Modules\Core\Attributes\Subscribe;
use Modules\Workspaces\Domain\Events\MemberInvited;
use Modules\Workspaces\Domain\Services\TeamMemberInvitationDispatcher;
use Modules\Workspaces\Domain\ValueObjects\Email;

#[Subscribe(MemberInvited::class)]
final readonly class SendInvitationEmailListener
{
    public function __construct(
        private TeamMemberInvitationDispatcher $invitationDispatcher,
    ) {
    }

    public function handle(MemberInvited $event): void
    {
        $this->invitationDispatcher->dispatch(
            email: Email::fromString($event->email),
            token: $event->token,
            workspaceId: $event->aggregateId(),
            role: $event->role,
        );
    }
}
