<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

final class WorkspaceCache
{
    private const TTL = 3600; // 1 hour

    public function getUserWorkspaces(string $userId): array
    {
        return Cache::remember(
            $this->userWorkspacesCacheKey($userId),
            self::TTL,
            fn () => $this->loadUserWorkspaces($userId)
        );
    }

    public function getWorkspaceSubscription(string $workspaceId): ?array
    {
        return Cache::remember(
            $this->workspaceSubscriptionCacheKey($workspaceId),
            self::TTL,
            fn () => $this->loadWorkspaceSubscription($workspaceId)
        );
    }

    public function invalidateUserWorkspaces(string $userId): void
    {
        Cache::forget($this->userWorkspacesCacheKey($userId));
    }

    public function invalidateWorkspaceSubscription(string $workspaceId): void
    {
        Cache::forget($this->workspaceSubscriptionCacheKey($workspaceId));

        // Also invalidate all users who have access to this workspace
        $userIds = DB::table('workspace_members')
            ->where('workspace_id', $workspaceId)
            ->pluck('user_id');

        foreach ($userIds as $userId) {
            $this->invalidateUserWorkspaces($userId);
        }
    }

    private function loadUserWorkspaces(string $userId): array
    {
        return DB::table('workspace_members')
            ->join('workspaces', 'workspace_members.workspace_id', '=', 'workspaces.id')
            ->leftJoin('subscriptions', function ($join) {
                $join->on('workspaces.id', '=', 'subscriptions.workspace_id')
                    ->whereIn('subscriptions.stripe_status', ['active', 'trialing', 'past_due', 'canceled']);
            })
            ->where('workspace_members.user_id', $userId)
            ->select(
                'workspaces.id',
                'workspaces.name',
                'workspaces.slug',
                'workspaces.avatar',
                'workspaces.plan',
                'workspaces.owner_id',
                'workspaces.pending_plan',
                'workspaces.plan_changes_at',
                'subscriptions.stripe_status as subscription_status',
                'subscriptions.ends_at as subscription_ends_at',
                'subscriptions.current_period_end as subscription_current_period_end'
            )
            ->get()
            ->map(fn ($w) => [
                'id' => $w->id,
                'name' => $w->name,
                'slug' => $w->slug,
                'avatar' => $w->avatar,
                'plan' => $w->plan,
                'ownerId' => $w->owner_id,
                'pendingPlan' => $w->pending_plan,
                'planChangesAt' => $w->plan_changes_at,
                'subscriptionStatus' => $w->subscription_status,
                'subscriptionEndsAt' => $w->subscription_ends_at,
                'subscriptionCurrentPeriodEnd' => $w->subscription_current_period_end,
            ])
            ->toArray();
    }

    private function loadWorkspaceSubscription(string $workspaceId): ?array
    {
        $workspace = DB::table('workspaces')
            ->leftJoin('subscriptions', function ($join) {
                $join->on('workspaces.id', '=', 'subscriptions.workspace_id')
                    ->whereIn('subscriptions.stripe_status', ['active', 'trialing', 'past_due', 'canceled']);
            })
            ->where('workspaces.id', $workspaceId)
            ->select(
                'workspaces.pending_plan',
                'workspaces.pending_billing_period',
                'workspaces.plan_changes_at',
                'subscriptions.stripe_status as subscription_status',
                'subscriptions.ends_at as subscription_ends_at',
                'subscriptions.current_period_end as subscription_current_period_end'
            )
            ->first();

        if (!$workspace) {
            return null;
        }

        return [
            'pendingPlan' => $workspace->pending_plan,
            'pendingBillingPeriod' => $workspace->pending_billing_period,
            'planChangesAt' => $workspace->plan_changes_at,
            'subscriptionStatus' => $workspace->subscription_status,
            'subscriptionEndsAt' => $workspace->subscription_ends_at,
            'subscriptionCurrentPeriodEnd' => $workspace->subscription_current_period_end,
        ];
    }

    private function userWorkspacesCacheKey(string $userId): string
    {
        return "user.{$userId}.workspaces";
    }

    private function workspaceSubscriptionCacheKey(string $workspaceId): string
    {
        return "workspace.{$workspaceId}.subscription";
    }
}
