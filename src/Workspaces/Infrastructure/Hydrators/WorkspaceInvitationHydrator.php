<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Hydrators;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Enums\InvitationStatus;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Models\WorkspaceInvitation;
use Modules\Workspaces\Domain\ValueObjects\Email;
use Modules\Workspaces\Domain\ValueObjects\InvitationToken;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;
use Modules\Workspaces\Infrastructure\Models\WorkspaceInvitationModel;

final readonly class WorkspaceInvitationHydrator
{
    public function toDomain(WorkspaceInvitationModel $model): WorkspaceInvitation
    {
        return WorkspaceInvitation::reconstitute([
            'id' => Id::fromString($model->id),
            'workspaceId' => WorkspaceId::fromString($model->workspace_id),
            'email' => Email::fromString($model->email),
            'role' => WorkspaceRole::from($model->role),
            'token' => InvitationToken::fromString($model->token),
            'status' => InvitationStatus::from($model->status),
            'invitedBy' => Id::fromString($model->invited_by),
            'createdAt' => new Date($model->created_at),
            'expiresAt' => $model->expires_at ? new Date($model->expires_at) : null,
            'acceptedAt' => $model->accepted_at ? new Date($model->accepted_at) : null,
        ]);
    }

    public function toModel(WorkspaceInvitation $invitation): WorkspaceInvitationModel
    {
        $model = new WorkspaceInvitationModel();
        $model->id = $invitation->id()->value();
        $model->workspace_id = $invitation->workspaceId()->value();
        $model->email = $invitation->email()->value;
        $model->role = $invitation->role()->value;
        $model->token = $invitation->token()->value;
        $model->status = $invitation->status()->value;
        $model->invited_by = $invitation->invitedBy()->value();
        $model->created_at = $invitation->createdAt();
        $model->expires_at = $invitation->expiresAt();
        $model->accepted_at = $invitation->acceptedAt();

        return $model;
    }
}
