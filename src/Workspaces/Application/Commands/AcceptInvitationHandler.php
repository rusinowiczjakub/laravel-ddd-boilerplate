<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Commands;

use Modules\Core\Attributes\CommandHandler;
use Modules\Core\Events\Contracts\EventBus;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Events\MemberJoined;
use Modules\Workspaces\Domain\Exceptions\InvitationAlreadyAcceptedException;
use Modules\Workspaces\Domain\Exceptions\InvitationExpiredException;
use Modules\Workspaces\Domain\Exceptions\InvitationNotFoundException;
use Modules\Workspaces\Domain\Models\WorkspaceMember;
use Modules\Workspaces\Domain\Repositories\WorkspaceInvitationRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Services\MemberInvitationSessionManager;
use Modules\Workspaces\Domain\ValueObjects\InvitationToken;

#[CommandHandler(AcceptInvitationCommand::class)]
final readonly class AcceptInvitationHandler
{
    public function __construct(
        private WorkspaceInvitationRepository $invitationRepository,
        private WorkspaceMemberRepository $memberRepository,
        private EventBus $eventBus,
        private MemberInvitationSessionManager $sessionManager,
    ) {
    }

    /**
     * @throws InvitationNotFoundException
     * @throws InvitationExpiredException
     * @throws InvitationAlreadyAcceptedException
     */
    public function handle(AcceptInvitationCommand $command): void
    {
        $invitation = $this->invitationRepository->findByToken(
            InvitationToken::fromString($command->token)
        );

        if (!$invitation) {
            throw new InvitationNotFoundException('Invitation not found');
        }

        // Accept invitation (validates expiration and status)
        $invitation->accept();

        // Create workspace member
        $member = WorkspaceMember::create(
            workspaceId: $invitation->workspaceId(),
            userId: Id::fromString($command->userId),
            role: $invitation->role(),
        );

        $this->memberRepository->save($member);
        $this->invitationRepository->save($invitation);

        // Emit event
        $this->eventBus->dispatch(
            new MemberJoined(
                workspaceId: $member->workspaceId()->value(),
                memberId: $member->id()->value(),
                userId: $member->userId()->value(),
                role: $member->role()->value,
            )
        );

        // Clear pending invitation from session
        $this->sessionManager->clearPendingInvitation();
    }
}
