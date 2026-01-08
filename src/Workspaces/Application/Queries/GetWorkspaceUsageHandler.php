<?php

declare(strict_types=1);

namespace Modules\Workspaces\Application\Queries;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Core\Attributes\QueryHandler;
use Modules\Workspaces\Application\Responses\UsageResponse;
use Modules\Workspaces\Domain\Models\Plan;

#[QueryHandler(GetWorkspaceUsageQuery::class)]
final readonly class GetWorkspaceUsageHandler
{
    public function handle(GetWorkspaceUsageQuery $query): UsageResponse
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // Get workspace plan
        $workspace = DB::table('workspaces')
            ->where('id', $query->workspaceId)
            ->select('plan')
            ->first();

        $plan = $workspace ? Plan::from($workspace->plan) : Plan::FREE;

        // Count events this month
        $eventsCount = DB::table('events')
            ->where('workspace_id', $query->workspaceId)
            ->whereBetween('received_at', [$startOfMonth, $endOfMonth])
            ->where('is_test', false)
            ->count();

        // Count members
        $membersCount = DB::table('workspace_members')
            ->where('workspace_id', $query->workspaceId)
            ->count();

        // Count workflows
        $workflowsCount = DB::table('workflows')
            ->where('workspace_id', $query->workspaceId)
            ->where('is_test', false)
            ->count();

        return new UsageResponse(
            eventsUsed: $eventsCount,
            eventsLimit: $plan->eventLimit(),
            notificationsUsed: 0,
            notificationsLimit: PHP_INT_MAX,  // Unlimited - clients use their own providers
            membersUsed: $membersCount,
            membersLimit: $plan->membersLimit(),
            workflowsUsed: $workflowsCount,
            workflowsLimit: $plan->workflowsLimit(),
        );
    }
}
