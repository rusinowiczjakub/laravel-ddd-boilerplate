<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Hydrators;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;
use Modules\Workspaces\Domain\Models\WorkspaceMember;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;
use Modules\Workspaces\Infrastructure\Models\WorkspaceMemberModel;

final readonly class WorkspaceMemberHydrator
{
    public function toDomain(WorkspaceMemberModel $model): WorkspaceMember
    {
        return WorkspaceMember::reconstitute([
            'id' => Id::fromString($model->id),
            'workspaceId' => WorkspaceId::fromString($model->workspace_id),
            'userId' => Id::fromString($model->user_id),
            'role' => WorkspaceRole::from($model->role),
            'addedAt' => new Date($model->added_at),
        ]);
    }

    public function toModel(WorkspaceMember $member): WorkspaceMemberModel
    {
        $model = new WorkspaceMemberModel();
        $model->id = $member->id()->value();
        $model->workspace_id = $member->workspaceId()->value();
        $model->user_id = $member->userId()->value();
        $model->role = $member->role()->value;
        $model->added_at = $member->addedAt();

        return $model;
    }
}
