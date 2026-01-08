<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Repositories;

use Modules\Workspaces\Domain\Models\Workspace;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\ValueObjects\Slug;
use Modules\Workspaces\Domain\ValueObjects\WorkspaceId;
use Modules\Workspaces\Infrastructure\Hydrators\WorkspaceHydrator;
use Modules\Workspaces\Infrastructure\Models\WorkspaceApiKeyModel;
use Modules\Workspaces\Infrastructure\Models\WorkspaceModel;

final readonly class EloquentWorkspaceRepository implements WorkspaceRepository
{
    public function __construct(
        private WorkspaceHydrator $hydrator,
    ) {
    }
    public function save(Workspace $workspace): void
    {
        $model = WorkspaceModel::query()->find($workspace->id()->value());

        if (!$model) {
            $model = $this->hydrator->toModel($workspace);
        } else {
            // Update existing model
            $updatedModel = $this->hydrator->toModel($workspace);
            $model->name = $updatedModel->name;
            $model->slug = $updatedModel->slug;
            $model->avatar = $updatedModel->avatar;
            $model->plan = $updatedModel->plan;
            $model->status = $updatedModel->status;
            $model->owner_id = $updatedModel->owner_id;
        }

        $model->save();

        // Save API keys
        foreach ($workspace->apiKeys()->all() as $apiKey) {
            $apiKeyModel = WorkspaceApiKeyModel::query()->find($apiKey->id()->value());

            if (!$apiKeyModel) {
                $apiKeyModel = $this->hydrator->apiKeyToModel($apiKey);
            } else {
                $updatedApiKeyModel = $this->hydrator->apiKeyToModel($apiKey);
                $apiKeyModel->workspace_id = $updatedApiKeyModel->workspace_id;
                $apiKeyModel->name = $updatedApiKeyModel->name;
                $apiKeyModel->key_hash = $updatedApiKeyModel->key_hash;
                $apiKeyModel->key_prefix = $updatedApiKeyModel->key_prefix;
                $apiKeyModel->is_test = $updatedApiKeyModel->is_test;
                $apiKeyModel->revoked_at = $updatedApiKeyModel->revoked_at;
                $apiKeyModel->last_used_at = $updatedApiKeyModel->last_used_at;
//                $apiKeyModel->expires_at = $updatedApiKeyModel->expires_at;
                $apiKeyModel->created_at = $updatedApiKeyModel->created_at;
            }

            $apiKeyModel->save();
        }
    }

    public function findById(WorkspaceId $id): ?Workspace
    {
        $model = WorkspaceModel::query()
            ->with('apiKeys')
            ->find($id->value());

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function findBySlug(Slug $slug): ?Workspace
    {
        $model = WorkspaceModel::query()
            ->with('apiKeys')
            ->where('slug', $slug->value)
            ->first();

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function findByApiKeyHash(string $hash): ?Workspace
    {
        $apiKeyModel = WorkspaceApiKeyModel::query()
            ->where('key_hash', $hash)
            ->first();

        if (!$apiKeyModel) {
            return null;
        }

        return $this->findById(WorkspaceId::fromString($apiKeyModel->workspace_id));
    }

    public function findByOwnerId(\Modules\Shared\Domain\ValueObjects\Id $ownerId): array
    {
        $models = WorkspaceModel::query()
            ->with('apiKeys')
            ->where('owner_id', $ownerId->value())
            ->get();

        return $models->map(fn(WorkspaceModel $model) => $this->hydrator->toDomain($model))->all();
    }
}
