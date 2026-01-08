<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Illuminate\Support\Facades\DB;
use Modules\Core\Attributes\QueryHandler;
use Modules\Workspaces\Application\Responses\MemberListResponse;
use Modules\Workspaces\Application\Responses\MemberResponse;
use Modules\Workspaces\Domain\Enums\InvitationStatus;
use Modules\Workspaces\Domain\Enums\WorkspaceRole;

#[QueryHandler(GetWorkspaceMembersQuery::class)]
final readonly class GetWorkspaceMembersHandler
{
    public function handle(GetWorkspaceMembersQuery $query): MemberListResponse
    {
        // Get workspace owner_id first
        $workspace = DB::table('workspaces')
            ->where('id', $query->workspaceId)
            ->select('owner_id')
            ->first();

        $ownerId = $workspace?->owner_id;

        // Get active members
        $memberRows = DB::table('workspace_members')
            ->join('users', 'workspace_members.user_id', '=', 'users.id')
            ->where('workspace_members.workspace_id', $query->workspaceId)
            ->select([
                'workspace_members.id',
                'workspace_members.user_id',
                'workspace_members.role',
                'workspace_members.added_at',
                'users.name',
                'users.email',
            ])
            ->orderBy('workspace_members.added_at', 'asc')
            ->get();

        $members = $memberRows->map(fn(object $row) => new MemberResponse(
            id: $row->id,
            userId: $row->user_id,
            name: $row->name,
            email: $row->email,
            role: WorkspaceRole::from($row->role),
            addedAt: $row->added_at,
            isOwner: $row->user_id === $ownerId,
            status: 'active',
        ))->all();

        // Get pending invitations
        $invitationRows = DB::table('workspace_invitations')
            ->where('workspace_id', $query->workspaceId)
            ->where('status', InvitationStatus::PENDING->value)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->select([
                'id',
                'email',
                'role',
                'created_at',
            ])
            ->orderBy('created_at', 'asc')
            ->get();

        $pendingInvitations = $invitationRows->map(fn(object $row) => new MemberResponse(
            id: $row->id, // Use invitation ID as the member ID for pending
            userId: null,
            name: null,
            email: $row->email,
            role: WorkspaceRole::from($row->role),
            addedAt: $row->created_at,
            isOwner: false,
            status: 'pending',
            invitationId: $row->id,
        ))->all();

        // Combine active members and pending invitations
        return new MemberListResponse([...$members, ...$pendingInvitations]);
    }
}
