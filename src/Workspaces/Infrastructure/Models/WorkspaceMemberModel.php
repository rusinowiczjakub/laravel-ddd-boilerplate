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
 * @property string $user_id
 * @property string $role
 * @property Carbon $added_at
 */
final class WorkspaceMemberModel extends Model
{
    use HasUuids;

    protected $table = 'workspace_members';

    public $timestamps = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'workspace_id',
        'user_id',
        'role',
        'added_at',
    ];

    protected $casts = [
        'added_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(WorkspaceModel::class, 'workspace_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\User::class, 'user_id');
    }
}
