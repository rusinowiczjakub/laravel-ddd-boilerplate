<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Hydrators;

use Modules\Shared\Domain\ValueObjects\Date;
use Modules\Shared\Domain\ValueObjects\Id;
use Modules\Workspaces\Domain\Collections\ApiKeyCollection;
use Modules\Workspaces\Domain\Enums\WorkspaceStatus;
use Modules\Workspaces\Domain\Models\Plan;
use Modules\Workspaces\Domain\Models\Workspace;
use Modules\Workspaces\Domain\Models\WorkspaceApiKey;
use Modules\Workspaces\Domain\ValueObjects\ApiKey;
use Modules\Workspaces\Domain\ValueObjects\Name;
use Modules\Workspaces\Domain\ValueObjects\Slug;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;
use Modules\Workspaces\Infrastructure\Models\WorkspaceApiKeyModel;
use Modules\Workspaces\Infrastructure\Models\WorkspaceModel;

final readonly class WorkspaceHydrator
{
    public function toDomain(WorkspaceModel $model): Workspace
    {
        $apiKeys = new ApiKeyCollection();

        foreach ($model->apiKeys as $apiKeyModel) {
            $apiKeys->push($this->apiKeyToDomain($apiKeyModel));
        }

        return Workspace::reconstitute([
            'id' => WorkspaceId::fromString($model->id),
            'name' => Name::fromString($model->name),
            'slug' => Slug::fromString($model->slug),
            'avatar' => $model->avatar,
            'plan' => Plan::from($model->plan),
            'status' => WorkspaceStatus::from($model->status),
            'ownerId' => Id::fromString($model->owner_id),
            'apiKeys' => $apiKeys,
            'createdAt' => new Date($model->created_at),
        ]);
    }

    public function toModel(Workspace $workspace): WorkspaceModel
    {
        $model = new WorkspaceModel();
        $model->id = $workspace->id()->value();
        $model->name = $workspace->name()->value;
        $model->slug = $workspace->slug()->value;
        $model->avatar = $workspace->avatar();
        $model->plan = $workspace->plan()->value;
        $model->status = $workspace->status()->value;
        $model->owner_id = $workspace->ownerId()->value();
        $model->created_at = $workspace->createdAt(); // Carbon accepts Carbon

        return $model;
    }

    public function apiKeyToDomain(WorkspaceApiKeyModel $model): WorkspaceApiKey
    {
        return WorkspaceApiKey::reconstitute([
            'id' => $model->id,
            'workspaceId' => Id::fromString($model->workspace_id),
            'apiKey' => ApiKey::fromHash($model->key_hash),
            'keyPrefix' => $model->key_prefix,
            'isTest' => $model->is_test,
            'name' => $model->name,
            'createdAt' => new Date($model->created_at),
            'lastUsedAt' => $model->last_used_at ? new Date($model->last_used_at) : null,
            'revokedAt' => $model->revoked_at ? new Date($model->revoked_at) : null,
        ]);
    }

    public function apiKeyToModel(WorkspaceApiKey $apiKey): WorkspaceApiKeyModel
    {
        $model = new WorkspaceApiKeyModel();
        $model->id = $apiKey->id()->value();
        $model->workspace_id = $apiKey->workspaceId()->value();
        $model->key_hash = $apiKey->apiKey()->hash;
        $model->key_prefix = $apiKey->keyPrefix();
        $model->is_test = $apiKey->isTest();
        $model->name = $apiKey->name();
        $model->created_at = $apiKey->createdAt(); // Date extends CarbonImmutable
        $model->last_used_at = $apiKey->lastUsedAt();
        $model->revoked_at = $apiKey->revokedAt();

        return $model;
    }
}
