<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property string $id
 * @property string $workspace_id
 * @property string $email
 * @property string $role
 * @property string $token
 * @property string $status
 * @property string $invited_by
 * @property Carbon $created_at
 * @property Carbon|null $expires_at
 * @property Carbon|null $accepted_at
 */
final class WorkspaceInvitationModel extends Model
{
    use HasUuids;

    protected $table = 'workspace_invitations';

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'id',
        'workspace_id',
        'email',
        'role',
        'token',
        'status',
        'invited_by',
        'created_at',
        'expires_at',
        'accepted_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'expires_at' => 'datetime',
        'accepted_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(WorkspaceModel::class, 'workspace_id');
    }

    public function inviter(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'invited_by');
    }
}
