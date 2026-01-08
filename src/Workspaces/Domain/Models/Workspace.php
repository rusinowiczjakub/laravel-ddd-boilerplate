<?php

declare(strict_types=1);

namespace Modules\Workspaces\Domain\Models;

use Modules\Core\Aggregate\AggregateRoot;
use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Collections\ApiKeyCollection;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Enums\WorkspaceStatus;
use Modules\Workspaces\Domain\Events\ApiKeyGenerated;
use Modules\Workspaces\Domain\Events\ApiKeyRevoked;
use Modules\Workspaces\Domain\Events\MemberInvited;
use Modules\Workspaces\Domain\Events\WorkspaceCreated;
use Modules\Workspaces\Domain\Exceptions\ApiKeyNotFoundException;
use Modules\Workspaces\Domain\ValueObjects\Email;
use Modules\Workspaces\Domain\ValueObjects\Name;
use Modules\Workspaces\Domain\ValueObjects\Slug;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;

class Workspace extends AggregateRoot
{
    private function __construct(
        private WorkspaceId $id,
        private Name $name,
        private Slug $slug,
        private ?string $avatar,
        private Plan $plan,
        private WorkspaceStatus $status,
        private Id $ownerId,
        private ApiKeyCollection $apiKeys,
        private Date $createdAt,
    ) {
    }

    public static function create(
        Name $name,
        Slug $slug,
        Plan $plan,
        Id $ownerId,
    ): self {
        $workspace = new self(
            id: WorkspaceId::create(),
            name: $name,
            slug: $slug,
            avatar: null,
            plan: $plan,
            status: WorkspaceStatus::ACTIVE,
            ownerId: $ownerId,
            apiKeys: new ApiKeyCollection(),
            createdAt: new Date(),
        );

        $workspace->record(new WorkspaceCreated(
            workspaceId: $workspace->id()->value(),
            name: $name->value,
            slug: $slug->value,
            plan: $plan->value,
            ownerId: $ownerId->value(),
        ));

        return $workspace;
    }

    /**
     * @param array{
     *     id: WorkspaceId,
     *     name: Name,
     *     slug: Slug,
     *     avatar: ?string,
     *     plan: Plan,
     *     status: WorkspaceStatus,
     *     ownerId: Id,
     *     apiKeys: ApiKeyCollection,
     *     createdAt: Date
     * } $data
     */
    public static function reconstitute(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['name'],
            slug: $data['slug'],
            avatar: $data['avatar'] ?? null,
            plan: $data['plan'],
            status: $data['status'],
            ownerId: $data['ownerId'],
            apiKeys: $data['apiKeys'],
            createdAt: $data['createdAt'],
        );
    }

    public function generateApiKey(bool $isTest = false, ?string $name = null): WorkspaceApiKey
    {
        $apiKey = WorkspaceApiKey::generate($this->id, $isTest, $name);
        $this->apiKeys->push($apiKey);

        $this->record(new ApiKeyGenerated(
            workspaceId: $this->id->value(),
            apiKeyId: $apiKey->id()->value(),
            keyHash: $apiKey->apiKey()->hash,
            name: $name,
            isTest: $isTest,
        ));

        return $apiKey;
    }

    public function revokeApiKey(Id $apiKeyId): void
    {
        $apiKey = $this->apiKeys->findById($apiKeyId);

        if (!$apiKey) {
            throw new ApiKeyNotFoundException('API key not found');
        }

        $apiKey->revoke();

        $this->record(new ApiKeyRevoked(
            workspaceId: $this->id->value(),
            apiKeyId: $apiKeyId->value(),
        ));
    }

    public function suspend(): void
    {
        $this->status = WorkspaceStatus::SUSPENDED;
    }

    public function activate(): void
    {
        $this->status = WorkspaceStatus::ACTIVE;
    }

    public function cancel(): void
    {
        $this->status = WorkspaceStatus::CANCELLED;
    }

    public function changePlan(Plan $plan): void
    {
        $this->plan = $plan;
    }

    public function rename(Name $name): void
    {
        $this->name = $name;
    }

    public function updateAvatar(?string $avatarPath): void
    {
        $this->avatar = $avatarPath;
    }

    public function transferOwnership(Id $newOwnerId): void
    {
        $this->ownerId = $newOwnerId;
    }

    public function inviteMember(Email $email, WorkspaceRole $role, Id $invitedBy): WorkspaceInvitation
    {
        $invitation = WorkspaceInvitation::create(
            workspaceId: $this->id,
            email: $email,
            role: $role,
            invitedBy: $invitedBy,
        );

        $this->record(new MemberInvited(
            workspaceId: $this->id->value(),
            invitationId: $invitation->id()->value(),
            email: $email->value,
            role: $role->value,
            invitedBy: $invitedBy->value(),
            token: $invitation->token()->value,
        ));

        return $invitation;
    }

    public function id(): WorkspaceId
    {
        return $this->id;
    }

    public function name(): Name
    {
        return $this->name;
    }

    public function slug(): Slug
    {
        return $this->slug;
    }

    public function avatar(): ?string
    {
        return $this->avatar;
    }

    public function plan(): Plan
    {
        return $this->plan;
    }

    public function status(): WorkspaceStatus
    {
        return $this->status;
    }

    public function ownerId(): Id
    {
        return $this->ownerId;
    }

    public function apiKeys(): ApiKeyCollection
    {
        return $this->apiKeys;
    }

    public function createdAt(): Date
    {
        return $this->createdAt;
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }
}
