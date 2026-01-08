<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Models;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Enums\InvitationStatus;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Exceptions\InvitationAlreadyAcceptedException;
use Modules\Workspaces\Domain\Exceptions\InvitationExpiredException;
use Modules\Workspaces\Domain\ValueObjects\Email;
use Modules\Workspaces\Domain\ValueObjects\InvitationToken;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

final class WorkspaceInvitation
{
    private function __construct(
        private Id $id,
        private WorkspaceId $workspaceId,
        private Email $email,
        private WorkspaceRole $role,
        private InvitationToken $token,
        private InvitationStatus $status,
        private Id $invitedBy,
        private Date $createdAt,
        private ?Date $expiresAt,
        private ?Date $acceptedAt,
    ) {
    }

    public static function create(
        WorkspaceId $workspaceId,
        Email $email,
        WorkspaceRole $role,
        Id $invitedBy,
    ): self {
        $now = new Date();

        return new self(
            id: Id::create(),
            workspaceId: $workspaceId,
            email: $email,
            role: $role,
            token: InvitationToken::generate(),
            status: InvitationStatus::PENDING,
            invitedBy: $invitedBy,
            createdAt: $now,
            expiresAt: $now->addDays(7), // 7 days expiration
            acceptedAt: null,
        );
    }

    /**
     * @param array{
     *     id: Id,
     *     workspaceId: WorkspaceId,
     *     email: Email,
     *     role: WorkspaceRole,
     *     token: InvitationToken,
     *     status: InvitationStatus,
     *     invitedBy: Id,
     *     createdAt: Date,
     *     expiresAt: Date|null,
     *     acceptedAt: Date|null
     * } $data
     */
    public static function reconstitute(array $data): self
    {
        return new self(
            id: $data['id'],
            workspaceId: $data['workspaceId'],
            email: $data['email'],
            role: $data['role'],
            token: $data['token'],
            status: $data['status'],
            invitedBy: $data['invitedBy'],
            createdAt: $data['createdAt'],
            expiresAt: $data['expiresAt'],
            acceptedAt: $data['acceptedAt'],
        );
    }

    /**
     * @throws InvitationExpiredException
     * @throws InvitationAlreadyAcceptedException
     */
    public function accept(): void
    {
        if ($this->isExpired()) {
            throw new InvitationExpiredException('Invitation has expired');
        }

        if ($this->isAccepted()) {
            throw new InvitationAlreadyAcceptedException('Invitation already accepted');
        }

        $this->status = InvitationStatus::ACCEPTED;
        $this->acceptedAt = new Date();
    }

    public function cancel(): void
    {
        $this->status = InvitationStatus::CANCELLED;
    }

    public function isExpired(): bool
    {
        if (!$this->expiresAt) {
            return false;
        }

        return $this->expiresAt->isPast();
    }

    public function isAccepted(): bool
    {
        return $this->status === InvitationStatus::ACCEPTED;
    }

    public function isPending(): bool
    {
        return $this->status === InvitationStatus::PENDING && !$this->isExpired();
    }

    public function id(): Id
    {
        return $this->id;
    }

    public function workspaceId(): WorkspaceId
    {
        return $this->workspaceId;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function role(): WorkspaceRole
    {
        return $this->role;
    }

    public function token(): InvitationToken
    {
        return $this->token;
    }

    public function status(): InvitationStatus
    {
        return $this->status;
    }

    public function invitedBy(): Id
    {
        return $this->invitedBy;
    }

    public function createdAt(): Date
    {
        return $this->createdAt;
    }

    public function expiresAt(): ?Date
    {
        return $this->expiresAt;
    }

    public function acceptedAt(): ?Date
    {
        return $this->acceptedAt;
    }
}
