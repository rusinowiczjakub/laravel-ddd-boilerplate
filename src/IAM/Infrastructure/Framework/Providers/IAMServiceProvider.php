<?php

declare(strict_types=1);

namespace Modules\IAM\Infrastructure\Framework\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Modules\IAM\Application\Listeners\SendEmailVerificationCode;
use Modules\IAM\Domain\Repositories\EmailVerificationSessionRepository;
use Modules\IAM\Domain\Repositories\UserRepository;
use Modules\IAM\Domain\Services\PostAuthenticationRedirectResolver;
use Modules\IAM\Domain\Services\TwoFactorService;
use Modules\IAM\Infrastructure\Repositories\DatabaseEmailVerificationSessionRepository;
use Modules\IAM\Infrastructure\Repositories\EloquentUserRepository;
use Modules\IAM\Infrastructure\Services\Google2FATwoFactorService;
use Modules\IAM\Infrastructure\Services\SessionBasedPostAuthenticationRedirectResolver;

final class IAMServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register repository bindings
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(EmailVerificationSessionRepository::class, DatabaseEmailVerificationSessionRepository::class);

        // Register service bindings
        $this->app->bind(TwoFactorService::class, Google2FATwoFactorService::class);
        $this->app->bind(PostAuthenticationRedirectResolver::class, SessionBasedPostAuthenticationRedirectResolver::class);
    }

    public function boot(): void
    {
        // Register migrations
        $this->loadMigrationsFrom(__DIR__ . '/../Migrations');

        // Register event listeners
        $this->registerEventListeners();
    }

    private function registerEventListeners(): void
    {
        Event::listen(
            Registered::class,
            SendEmailVerificationCode::class,
        );
    }
}
