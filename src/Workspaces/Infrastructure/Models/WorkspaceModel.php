<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;

/**
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $avatar
 * @property string $plan
 * @property string $status
 * @property string $owner_id
 * @property Carbon $created_at
 */
final class WorkspaceModel extends Model
{
    use HasUuids, Billable;

    protected $table = 'workspaces';

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'avatar',
        'plan',
        'pending_plan',
        'pending_billing_period',
        'plan_changes_at',
        'status',
        'owner_id',
        'stripe_id',
        'pm_type',
        'pm_last_four',
        'trial_ends_at',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'trial_ends_at' => 'datetime',
        'plan_changes_at' => 'datetime',
    ];

    public function apiKeys(): HasMany
    {
        return $this->hasMany(WorkspaceApiKeyModel::class, 'workspace_id');
    }

    /**
     * Override Billable trait's subscriptions() to use 'workspace_id' instead of 'workspace_model_id'.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'workspace_id')->orderBy('created_at', 'desc');
    }
}
