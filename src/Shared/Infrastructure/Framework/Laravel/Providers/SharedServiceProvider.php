<?php

namespace Modules\Shared\Infrastructure\Framework\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Shared\Domain\RateLimiting\RateLimiter;
use Modules\Shared\Domain\Storage\Storage;
use Modules\Shared\Infrastructure\Storage\IlluminateStorage;
use Modules\Workspaces\Infrastructure\RateLimiting\IlluminateWorkspaceRateLimiter;

class SharedServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(Storage::class, IlluminateStorage::class);
        $this->app->singleton(RateLimiter::class, IlluminateWorkspaceRateLimiter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }
}
