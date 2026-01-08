<?php

declare(strict_types=1);

namespace Modules\Workspaces\Infrastructure\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Workspaces\Domain\Repositories\WorkspaceInvitationRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceMemberRepository;
use Modules\Workspaces\Domain\Repositories\WorkspaceRepository;
use Modules\Workspaces\Domain\Services\WorkspaceSlugGenerator;
use Modules\Workspaces\Infrastructure\Repositories\EloquentWorkspaceInvitationRepository;
use Modules\Workspaces\Infrastructure\Repositories\EloquentWorkspaceMemberRepository;
use Modules\Workspaces\Infrastructure\Repositories\EloquentWorkspaceRepository;
use Modules\Workspaces\Infrastructure\Services\EloquentWorkspaceSlugGenerator;

final class WorkspacesServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register repository bindings
        $this->app->bind(WorkspaceRepository::class, EloquentWorkspaceRepository::class);
        $this->app->bind(WorkspaceMemberRepository::class, EloquentWorkspaceMemberRepository::class);
        $this->app->bind(WorkspaceInvitationRepository::class, EloquentWorkspaceInvitationRepository::class);
        $this->app->bind(WorkspaceSlugGenerator::class, EloquentWorkspaceSlugGenerator::class);

        // Register domain services
        $this->app->bind(
            \Modules\Workspaces\Domain\Services\TeamMemberInvitationDispatcher::class,
            \Modules\Workspaces\Infrastructure\Services\MailTeamMemberInvitationDispatcher::class,
        );
        $this->app->bind(
            \Modules\Workspaces\Domain\Services\UserExistenceChecker::class,
            \Modules\Workspaces\Infrastructure\Services\EloquentUserExistenceChecker::class,
        );
        $this->app->bind(
            \Modules\Workspaces\Domain\Services\MemberInvitationSessionManager::class,
            \Modules\Workspaces\Infrastructure\Services\LaravelMemberInvitationSessionManager::class,
        );
    }

    public function boot(): void
    {
        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        // Event listeners are auto-registered via #[Subscribe] attribute
    }
}
