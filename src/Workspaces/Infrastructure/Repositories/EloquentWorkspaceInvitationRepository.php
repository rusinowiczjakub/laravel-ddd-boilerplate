<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Repositories;

use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Models\WorkspaceInvitation;
use Modules\Workspaces\Domain\Repositories\WorkspaceInvitationRepository;
use Modules\Workspaces\Domain\ValueObjects\InvitationToken;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;
use Modules\Workspaces\Infrastructure\Hydrators\WorkspaceInvitationHydrator;
use Modules\Workspaces\Infrastructure\Models\WorkspaceInvitationModel;

final readonly class EloquentWorkspaceInvitationRepository implements WorkspaceInvitationRepository
{
    public function __construct(
        private WorkspaceInvitationHydrator $hydrator,
    ) {
    }

    public function save(WorkspaceInvitation $invitation): void
    {
        $model = WorkspaceInvitationModel::query()->find($invitation->id()->value());

        if (!$model) {
            $model = $this->hydrator->toModel($invitation);
        } else {
            $updatedModel = $this->hydrator->toModel($invitation);
            $model->workspace_id = $updatedModel->workspace_id;
            $model->email = $updatedModel->email;
            $model->role = $updatedModel->role;
            $model->token = $updatedModel->token;
            $model->status = $updatedModel->status;
            $model->invited_by = $updatedModel->invited_by;
            $model->created_at = $updatedModel->created_at;
            $model->expires_at = $updatedModel->expires_at;
            $model->accepted_at = $updatedModel->accepted_at;
        }

        $model->save();
    }

    public function findById(Id $id): ?WorkspaceInvitation
    {
        $model = WorkspaceInvitationModel::query()->find($id->value());

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function findByToken(InvitationToken $token): ?WorkspaceInvitation
    {
        $model = WorkspaceInvitationModel::query()
            ->where('token', $token->value)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function findPendingByWorkspace(WorkspaceId $workspaceId): array
    {
        $models = WorkspaceInvitationModel::query()
            ->where('workspace_id', $workspaceId->value())
            ->where('status', 'pending')
            ->get();

        return $models->map(fn(WorkspaceInvitationModel $model) => $this->hydrator->toDomain($model))->all();
    }
}
