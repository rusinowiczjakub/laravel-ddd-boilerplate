<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Repositories;

use Modules\Billing\Domain\Models\BillingWorkspace;
use Modules\Billing\Domain\Repositories\BillingWorkspaceRepository;
use Modules\Billing\Infrastructure\Hydrators\BillingWorkspaceHydrator;
use Modules\Billing\Infrastructure\Models\WorkspaceModel;
use Modules\Shared\Domain\Exceptions\DomainException;
use Modules\Shared\Domain\ValueObjects\Uuid;

final readonly class EloquentBillingWorkspaceRepository implements BillingWorkspaceRepository
{
    public function __construct(
        private BillingWorkspaceHydrator $hydrator,
    ) {}

    public function findById(Uuid $workspaceId): ?BillingWorkspace
    {
        $model = WorkspaceModel::find($workspaceId->value());

        if (!$model) {
            return null;
        }

        return $this->hydrator->toDomain($model);
    }

    public function save(BillingWorkspace $workspace): void
    {
        $model = WorkspaceModel::find($workspace->id()->value());

        if (!$model) {
            throw new DomainException('Workspace not found');
        }

        // Sync domain changes to infrastructure model
        $this->hydrator->syncToModel($workspace, $model);

        $model->save();
    }
}
