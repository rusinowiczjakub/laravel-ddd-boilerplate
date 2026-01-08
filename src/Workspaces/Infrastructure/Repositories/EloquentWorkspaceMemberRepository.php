<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Repositories;

use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Models\WorkspaceMember;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;
use Modules\Workspaces\Infrastructure\Hydrators\WorkspaceMemberHydrator;
use Modules\Workspaces\Infrastructure\Models\WorkspaceMemberModel;

final readonly class EloquentWorkspaceMemberRepository implements WorkspaceMemberRepository
{
    public function __construct(
        private WorkspaceMemberHydrator $hydrator,
    ) {
    }

    public function save(WorkspaceMember $member): void
    {
        $model = WorkspaceMemberModel::query()->find($member->id()->value());

        if (!$model) {
            $model = $this->hydrator->toModel($member);
        } else {
            $updatedModel = $this->hydrator->toModel($member);
            $model->workspace_id = $updatedModel->workspace_id;
            $model->user_id = $updatedModel->user_id;
            $model->role = $updatedModel->role;
            $model->added_at = $updatedModel->added_at;
        }

        $model->save();
    }

    public function delete(WorkspaceMember $member): void
    {
        WorkspaceMemberModel::query()
            ->where('id', $member->id()->value())
            ->delete();
    }

    public function findById(Id $id): ?WorkspaceMember
    {
        $model = WorkspaceMemberModel::query()->find($id->value());

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function findByWorkspaceAndUser(WorkspaceId $workspaceId, Id $userId): ?WorkspaceMember
    {
        $model = WorkspaceMemberModel::query()
            ->where('workspace_id', $workspaceId->value())
            ->where('user_id', $userId->value())
            ->first();

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function findByWorkspace(WorkspaceId $workspaceId): array
    {
        $models = WorkspaceMemberModel::query()
            ->where('workspace_id', $workspaceId->value())
            ->get();

        return $models->map(fn(WorkspaceMemberModel $model) => $this->hydrator->toDomain($model))->all();
    }

    public function findByUser(Id $userId): array
    {
        $models = WorkspaceMemberModel::query()
            ->where('user_id', $userId->value())
            ->get();

        return $models->map(fn(WorkspaceMemberModel $model) => $this->hydrator->toDomain($model))->all();
    }
}
