<?php

declare(strict_types=1);

namespace Modules\Billing\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Subscription;

/**
 * WorkspaceModel - Billing module's view of the workspace.
 * Maps to the same 'workspaces' table but only includes billing-related fields.
 *
 * @property string $id
 * @property string $name
 * @property string $plan
 * @property string|null $pending_plan
 * @property string|null $pending_billing_period
 * @property Carbon|null $plan_changes_at
 * @property string|null $stripe_id
 * @property string|null $pm_type
 * @property string|null $pm_last_four
 * @property Carbon|null $trial_ends_at
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
        'plan',
        'pending_plan',
        'pending_billing_period',
        'plan_changes_at',
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

    /**
     * Override Billable trait's subscriptions() to use 'workspace_id' instead of 'workspace_model_id'.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class, 'workspace_id')->orderBy('created_at', 'desc');
    }
}
